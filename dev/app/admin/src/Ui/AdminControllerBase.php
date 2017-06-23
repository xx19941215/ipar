<?php
namespace Admin\Ui;

class AdminControllerBase extends \Gap\Routing\Controller
{
    protected function getCurrentUser()
    {
        return current_user();
    }
}
