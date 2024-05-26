<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Doctor;

use App\Models\Appointment;

class AdminController extends Controller
{
    public function addview() {
        return view('admin.add_doctor');
    }

      // Validate the incoming request data
        public function upload(Request $request)
        {
            try {
                // Validate the incoming request data
                $request->validate([
                    'name' => 'required|string|max:255',
                    'phone' => 'required|numeric',
                    'spealicity' => 'required|string', // Corrected from spealicity to speciality
                    'room' => 'required|string|max:255',
                    'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                $doctor = new Doctor;

                // File Upload (Potential Errors)
                $image = $request->file('file');
                if (!$image) {
                    // Handle error: No image uploaded
                    return redirect()->back()->withErrors(['error' => 'Please select an image for the doctor.']);
                }

                $imagename = time() . '.' . $image->getClientOriginalExtension();

                // File Move (Potential Errors)
                $image->move(public_path('doctorimage'), $imagename);

                // Doctor Data Assignment
                $doctor->image = $imagename;
                $doctor->name = $request->name;
                $doctor->phone = $request->phone;
                $doctor->spealicity = $request->spealicity; // Corrected typo
                $doctor->room = $request->room;

                // Database Save
                $doctor->save();

                // Redirect to a success page or back with success message
                return redirect()->back()->with('success', 'Doctor information uploaded successfully.');
            } catch (Exception $e) {
                // Handle database errors or other exceptions
                \Log::error('Failed to upload doctor information: ' . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Doctor upload failed: ' . $e->getMessage()]);
            }
        }

      public function showappointment() {

        $data=appointment::all();

        return view('admin.showappointment',compact('data'));
      }

      public function approved($id) {
        $data=appointment::find($id);
        $data->status='approved';
        $data->save();
        return redirect()->back();
      }

      public function canceled($id) {
        $data=appointment::find($id);
        $data->status='canceled';
        $data->save();
        return redirect()->back();
      }

      public function showdoctor() {
        $data=doctor::all();
        return view('admin.showdoctor',compact('data'));
      }

      public function deletedoctor($id) {
        $data= doctor::find($id);
        $data->delete();
        return redirect()->back();
      }

      public function updatedoctor($id) {
        $data= doctor::find($id);
        return view('admin.update_doctor',compact('data'));

      }

      public function editdoctor(Request $request,$id) {
        $doctor= doctor::find($id);
        $doctor->name=$request->name;
        $doctor->room=$request->room;
        $doctor->phone=$request->phone;
        $doctor->save();
        return redirect()->back();

      }
}

