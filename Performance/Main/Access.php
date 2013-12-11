<?php


class Performance_Main_Access {
    private $_provider;

    public function __construct(Performance_Main_Provider $provider) {
        $this->_provider = $provider;
    }

    public function checkAccess() {
        $this->_provider->get('Performance_Main_Access_AllowFrom')->checkAccess();
        $this->_provider->get('Performance_Main_Access_DeniedFrom')->checkAccess();
    }

}
