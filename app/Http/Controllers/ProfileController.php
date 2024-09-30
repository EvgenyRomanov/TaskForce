<?php

namespace App\Http\Controllers;

use App\Actions\Rating\PlaceInRankingAction;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Category;
use App\Models\CategoryUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('components.user.edit-profile', [
            'user' => $request->user(),
            'categories' => Category::all()
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $data = $request->all();
        $user = $request->user();

        if (isset($data['avatar'])) {
            /** @var UploadedFile $avatar */
            $avatar = $data['avatar'];
            $data['avatar'] = basename($avatar->store("public/{$user->id}"));
        }

        $categories = $data['categories'] ?? [];
        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        foreach ($categories as $category) {
            CategoryUser::query()->firstOrCreate([
                'executor_id' => $request->user()->id,
                'category_id' => $category
            ]);
        }

        return Redirect::route('profile.edit');
    }

    public function display(Request $request, PlaceInRankingAction $rankingAction, ?User $user = null): View
    {
        $user = $user ?? $request->user();
        if ($user->isCustomer()) abort(404);
        $ratingExecutors = $rankingAction($user->id);

        return view('components.user.profile', [
            'user' => $user,
            'ratingExecutors' => $ratingExecutors
        ]);
    }
}