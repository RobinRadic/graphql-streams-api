<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL\Schema;

use Illuminate\Http\Request;

class Context
{
    /**
     * Http request.
     *
     * @var Request
     */
    public $request;

    /**
     * Authenticated user.
     *
     * May be null since some fields may be accessible without authentication.
     *
     * @var \Anomaly\UsersModule\User\Contract\UserInterface|null
     */
    public $user;

    /**
     * Create new context.
     *
     * @param Request $request
     * @param mixed|null $user
     */
    public function __construct(Request $request, $user = null)
    {
        $this->request = $request;
        $this->user = $user;
    }
}