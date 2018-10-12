<?php /** @noinspection ArraySearchUsedAsInArrayInspection */
/** @noinspection SubStrUsedAsArrayAccessInspection */

/**
 * This is a blocking PHP process that will watch the files in a specific directory
 * 1 level deep an emit events when files change accordingly. You can attach event
 * observers to the different file events to react to.
 *
 * @author Ben Carter
 * @since  2015-06-10
 */

namespace Radic\GraphqlStreamsApiModule\Support;

use Illuminate\Events\Dispatcher;

class FileWatcher extends Dispatcher
{
    /**
     * Event list for internal file watch events
     */
    const CREATE = 'create';
    /**
     *
     */
    const MODIFIED = 'modified';
    /**
     *
     */
    const DELETE = 'delete';

    /**
     * list of files currently pulled from the directory being watched
     *
     * @var array
     */
    protected $files = [];

    /**
     * List of previous files scanned from the watch directory
     *
     * @var array
     */
    protected $lastFiles = [];

    /**
     * Glob pattern used in glob method call to restrict results
     *
     * @var string
     */
    protected $pattern;

    /**
     * Name of the directory to watch for changes
     *
     * @var string
     */
    protected $directory;

    /**
     * Number of milliseconds to wait before rescanning the directory
     *
     * @var int
     */
    protected $interval;

    /**
     * list of current file md5 hashes for comparison
     *
     * @var array
     */
    protected $fileHashes = [];

    /**
     * list of previous file md5 hashes for comparison
     *
     * @var array
     */
    protected $lastFileHashes = [];

    /**
     * Options to pass into the glob function
     *
     * @var integer
     */
    protected $globOptions;

    /**
     * Tell the system to stop after set number of iterations.
     * The default is to run forever.
     *
     * @var int
     */
    protected $iterations = -1;

    /**
     * This will setup the Directory Watcher.
     *
     * @param string $directory
     * @param string $pattern
     * @param int    $interval in milliseconds
     *
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        parent::__construct();
        $this->setGlobOptions(GLOB_BRACE);
    }

    /**
     * watch method
     *
     * @param        $directory
     * @param string $pattern
     * @param int    $interval
     *
     * @return $this
     */
    public function watch($directory, $pattern = '*.{*}', $interval = 10)
    {
        if (empty($directory)) {
            throw new \InvalidArgumentException('You must pass in a non-empty directory to watch');
        }

        if ( ! is_dir($directory)) {
            throw new \InvalidArgumentException("The directory $directory does not exist on the file system. ");
        }

        if ( ! \is_int($interval)) {
            throw new \InvalidArgumentException('The interval must be va valid integer to set the microseconds to watch');
        }

        $this->directory = $directory;
        $this->pattern   = $pattern;
        $this->setInterval($interval);
        $this->start();
    }

    /**
     * on method
     *
     * @param $event
     * @param $listener
     *
     * @return $this
     */
    public function on($event, $listener)
    {
        $this->listen($event, $listener);
        return $this;
    }

    /**
     * created method
     *
     * @param $listener
     *
     * @return \Radic\GraphqlStreamsApiModule\Support\FileWatcher
     */
    public function created($listener)
    {
        return $this->on(self::CREATE, $listener);
    }

    /**
     * modified method
     *
     * @param $listener
     *
     * @return \Radic\GraphqlStreamsApiModule\Support\FileWatcher
     */
    public function modified($listener)
    {
        return $this->on(self::MODIFIED, $listener);
    }

    /**
     * deleted method
     *
     * @param $listener
     *
     * @return \Radic\GraphqlStreamsApiModule\Support\FileWatcher
     */
    public function deleted($listener)
    {
        return $this->on(self::DELETE, $listener);
    }

    /**
     * Start the daemon watcher process which is going to block the request. It will
     * continue to watch the file system until the php script is killed at a terminal level.
     */
    protected function start()
    {
        $counter = 0;
        while ($this->getIterations() !== $counter) {
            $files = $this->getLastFiles();

            // if we do not have any files from last run we are at first run and have to setup process.
            if (empty($files)) {
                $this->lastFiles = $this->findFiles();
                $this->files     = $this->lastFiles;
                $this->hashFiles();
                $this->lastFileHashes = $this->fileHashes;
            } else {
                $this->lastFiles      = $this->getFiles();
                $this->files          = $this->findFiles();
                $this->lastFileHashes = $this->getFileHashes();
                $this->hashFiles();
            }

            $this->detectCreateOrDelete();
            $this->detectChange();
            $counter++;

            usleep($this->getInterval());
        }
    }

    /**
     * Assign a md5 hash of the file content
     *
     * @throws \RuntimeException
     */
    public function hashFiles()
    {
        clearstatcache();
        $files = $this->getFiles();
        if (empty($files) || ! is_array($files)) {
            throw new \RuntimeException('Could not obtain a list of files to hash for watching.');
        }
        foreach ($files as $filename) {
            $this->fileHashes[ $filename ] = md5_file($filename);
        }
    }

    /**
     * This will pull the current file system and the previous file system state
     * and detect create / delete changes but examining the delta between current run
     * state on disk versus previous run state.
     */
    public function detectCreateOrDelete()
    {
        $last    = $this->getLastFiles();
        $current = $this->getFiles();

        $diff = array_merge(array_diff($last, $current), array_diff($current, $last));

        if (empty($diff)) {
            return;
        }

        // if the file list has changed then we need to handle to two cases either (new file or removed file)
        foreach ($diff as $key => $file) {

            // if the file is found in the current list of files on disk its a new file
            if (array_search($file, $current, true) !== false) {
                $this->fire(self::CREATE, [ $file ]);
            }

            // if the file exists in the previous file list then know it was removed
            if (array_search($file, $last, true) !== false) {
                $this->fire(self::DELETE, [ $file ]);
            }
        }
    }

    /**
     * Detect file modification changes by comparing the last run of filemtime
     * against the current run of filemtime. This will emit a modified event.
     */
    public function detectChange()
    {
        $fileHashes     = $this->getFileHashes();
        $lastFileHashes = $this->getLastFileHashes();

        if (empty($fileHashes)) {
            return;
        }

        foreach ($fileHashes as $fileName => $modified) {
            if (isset($this->lastFileHashes[ $fileName ]) && $modified != $lastFileHashes[ $fileName ]) {
                $this->fire(self::MODIFIED, [ $fileName ]);
            }
        }
    }

    /**
     * Find only files in the directory assume the files all have an identifier type of period.
     *
     * @return array
     */
    public function findFiles()
    {
        if (substr($this->directory, -1, 1) === '/') {
            $lookup = $this->getDirectory() . $this->getPattern();
        } else {
            $lookup = $this->getDirectory() . '/' . $this->getPattern();
        }

        return glob($lookup, $this->getGlobOptions());
    }


    /**
     * Number in milliseconds to wait before rescanning the watch directory for changes.
     *
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Get the glob pattern that will be used to filter the files from the directory
     * defaults to "*.{*}" all files with file identifier
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Get the watch directory
     *
     * @return mixed
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Get the current file listing for this watch segment
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Get the last scan of the files from the process
     *
     * @return array
     */
    public function getLastFiles()
    {
        return $this->lastFiles;
    }

    /**
     * Return the glob options that are set.
     *
     * @return int
     */
    public function getGlobOptions()
    {
        return $this->globOptions;
    }

    /**
     * @return array
     */
    public function getFileHashes()
    {
        return $this->fileHashes;
    }

    /**
     * @return array
     */
    public function getLastFileHashes()
    {
        return $this->lastFileHashes;
    }

    /**
     * @return int
     */
    public function getIterations()
    {
        return $this->iterations;
    }

    /**
     * Override the default glob options
     *
     * @see http://php.net/manual/en/function.glob.php
     *
     * @param integer $options
     */
    public function setGlobOptions($options)
    {
        $this->globOptions = $options;
    }

    /**
     * The number of iterations to run the daemon process
     *
     * @param int $iterations
     */
    public function setIterations($iterations)
    {
        $this->iterations = $iterations;
    }

    /**
     * @param int $interval
     */
    public function setInterval($interval)
    {
        $this->interval = $interval * 1000; // convert to milliseconds
    }

}