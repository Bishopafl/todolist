<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;

class ToDoController extends Controller
{
    // index view
    public function index() {
        /*
         * Administrator settings
         */
        if (Auth::user()->is_admin) {
            $coworkers = Invitation::where('admin_id',Auth::user()->id)->where('accepted',1)->get();
            $invitations = Invitation::where('admin_id',Auth::user()->id)->where('accepted',0)->get();
            $tasks = Task::where( 'user_id',Auth::user()->id)->orWhere('admin_id', Auth::user()->id)->orderBy('created_at','DESC')->paginate(4);
        } else {
            /*
            * User settings
            */
            // get all tasks and set into variable
            $invitations = [];
            $tasks = Task::where( 'user_id',Auth::user()->id)->orderBy('created_at','DESC')->paginate(4);
            $coworkers = User::where('is_admin',1)->get();
        }
        return view('index', compact('tasks','coworkers','invitations'));
    }
    // Store Task
    public function store(Request $request) {
        // only save information if someone puts something on the form
        if ( $request->input('task') ) {
            $task = new Task;
            $task->content = $request->input('task');
            // If the user is admin...
            if(Auth::user()->is_admin) {
                // And assigned to is equal to user id
                if ($request->input('assignTo') == Auth::user()->id) {
                    Auth::user()->tasks()->save($task);
                } elseif($request->input('assignTo') != null) {
                    $task->user_id = $request->input('assignTo');
                    $task->admin_id = Auth::user()->id;
                    $task->save();
                }
            } else {
                Auth::user()->tasks()->save($task);
            }



        }
        return redirect()->back();
    }
    // Edit Task
    public function edit($id) {
        $task = Task::find($id);
        if (Auth::user()->is_admin){
            $coworkers = Invitation::where('admin_id',Auth::user()->id)->where('accepted',1)->get();
            $invitations = Invitation::where('admin_id',Auth::user()->id)->where('accepted',0)->get();
        } else {
            $coworkers = [];
            $invitations = [];
        }
        /*
         * Code below can also look like a standard array instead of using compact()
         *
         * return view('edit', ['task'=>$task]);
        */
        return view('edit', compact('task','invitations', 'coworkers'));

    }
    // Update Task
    public function update($id, Request $request) {
        // Check if there is a task request from the input then update
        if ($request->input('task')) {
            $task = Task::find($id);
            $task->content = $request->input('task');
            if (Auth::user()->is_admin) {
                if ($request->input('assignTo') == Auth::user()->id) {
                    Auth::user()->tasks()->save($task);
                } elseif($request->input('assignTo') != null) {
                    $task->user_id = $request->input('assignTo');
                    $task->admin_id = Auth::user()->id;
                    $task->save();
                }
            } else {
                if ($this->_authorize($invitation->user_id))
                $task->save();
            }
        }
        return redirect('/');
    }
    // Delete Task
    public function delete($id) {

        $task = Task::find($id);

        if (!Auth::user()->is_admin) {
            if (!$this->_authorize($task->user_id)) {
                return redirect()->back();
            }
        }

        $task->delete();
        return redirect()->back();
    }
    // Handles Updating Status of a Task
    public function updateStatus($id) {

        $task = Task::find($id);
        $task->status = !$task->status;
        if ($this->_authorize($task->user_id))
        $task->save();
        return redirect()->back();
    }

    public function sendInvitation(Request $request) {
        if ( (int)$request->input('admin') > 0 && !Invitation::where('worker_id',Auth::user()->id)->where('admin_id')->exists()) {
            $invitation = new Invitation;
            $invitation->worker_id = Auth::user()->id;
            $invitation->admin_id = (int)$request->input('admin');
            $invitation->save();
        }
        return redirect()->back();
    }

    public function acceptInvitation($id) {
        $invitation = Invitation::find($id);
        $invitation->accepted = true;
        if ($this->_authorize($invitation->admin_id))
        $invitation->save();

        return redirect()->back();
    }

    public function denyInvitation($id) {
        $invitation = Invitation::find($id);
        if ($this->_authorize($invitation->admin_id))
        $invitation->delete();

        return redirect()->back();
    }

    public function deleteWorker($id) {
        $invitation = Invitation::find($id);
        if ($this->_authorize($invitation->admin_id))
        $invitation->delete();

        return redirect()->back();
    }

    protected function _authorize($id) {
        return Auth::user()->id === $id ? true : false;
    }

}
