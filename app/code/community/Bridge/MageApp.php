<?php
namespace Bridge;

interface MageApp
{
    public function useCache($model);

    public function getStore($id = null);
}
