<?php

namespace App\Controllers;

class Pages extends BaseController
{
	public function view($page = 'home')
	{
        if ( ! file_exists(APPPATH.'/Views/Pages/'.$page.'.php'))
        {
            // Whoops, we don't have a page for that!
            throw new \CodeIgniter\PageNotFoundException($page);
        }

        $data['title'] = ucfirst($page); // Capitalize the first letter

        echo view('Pages/'.$page, $data);
	}

    public function index()
    {
        return view('welcome_message');
    }
}
