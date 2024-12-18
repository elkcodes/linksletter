<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Models\Link;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $links = Link::query()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('links.index', [
            'links' => $links,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::all();

        return view('links.create', ['users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLinkRequest $request): RedirectResponse
    {
        $link = Link::query()
            ->create([
                ...$request->validated(),
                'user_id' => auth()->id(),
            ]);

        if (! $link->position) {

            $link->position = Link::max('position') + 1;

            $link->save();
        }

        return redirect()
            ->route('links.index')
            ->with('message', 'Link created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Link $link): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Link $link): View
    {
        abort_unless($link->user_id === auth()->id(), 404);

        $users = User::all();

        return view('links.edit', [
            'link' => $link,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLinkRequest $request, Link $link): RedirectResponse
    {
        abort_unless($link->user_id === auth()->id(), 404);

        $link->update($request->validated());

        return redirect()
            ->route('links.index')
            ->with('message', 'Link updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Link $link): RedirectResponse
    {
        abort_unless($link->user_id === auth()->id(), 404);

        $link->delete();

        return redirect()
            ->route('links.index')
            ->with('message', 'Link deleted successfully.');
    }
}
