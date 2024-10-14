<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        // Retrieve the currently authenticated user
        $user = auth()->user();

        // Pass user data to the view
        return view('profile.edit', compact('user'));
    }
    public function update(Request $request)
{
    $user = auth()->user();
    
    // Validate and update user data
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
    ]);

    $user->update($data);

    return redirect()->route('profile.edit')->with('success', 'Profile updated successfully');
}

}
