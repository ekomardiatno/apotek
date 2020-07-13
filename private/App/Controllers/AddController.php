<?php

class AddController extends Controller {
    public function consult()
    {
        $this->_web->title('Pendaftaran');
        $this->_web->breadcrumb([
            [
                'add.consult', 'Pendaftaran'
            ]
        ]);
        $this->_web->view('consult');
    }
}