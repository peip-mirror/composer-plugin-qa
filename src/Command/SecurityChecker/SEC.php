<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command\SecurityChecker;

/**
 * Alias for qa:security-checker
 */
class SEC extends SecurityChecker
{
    /**
     * Console params configuration
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:sec');
    }
}
