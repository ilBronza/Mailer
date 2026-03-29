<?php

namespace IlBronza\Mailer\Http\Middleware;

use IlBronza\CRUD\Middleware\CRUDBasePackageMiddlewareRolesPermissions;

/**
 * Resolves allowed roles for Mailer routes from config (mailer.defaultRoles / mailer.routeRoles).
 */
class MailerMiddlewareRolesPermissions extends CRUDBasePackageMiddlewareRolesPermissions
{
    protected string $configPackageName = 'mailer';
}
