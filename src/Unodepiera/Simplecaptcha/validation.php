<?php

Validator::extend('captcha', function($attribute, $captcha, $params)
{
    return Simplecaptcha::check($captcha);
});