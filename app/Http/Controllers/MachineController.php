<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function view_all_machine(){
        return view('pages.machine.view_all_machine');
    }
    public function view_machine_detail(){
        return view('pages.machine.view_all_machine_detail');
    }
    public function view_list_machine_install(){
        return view('pages.machine.list_machine_install');
    }
    public function view_machine_location(){
        return view('pages.machine.machine_location');
    }
}
