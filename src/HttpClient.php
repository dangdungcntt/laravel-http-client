<?php

namespace Nddcoder\HttpClient;

interface HttpClient
{
    public function get($url, $options = []);

    public function post($url, $options = []);

    public function put($url, $options = []);

    public function delete($url, $options = []);

    public function request($method, $url, $options = []);
}
