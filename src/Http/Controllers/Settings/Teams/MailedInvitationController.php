<?php

namespace Laravel\Spark\Http\Controllers\Settings\Teams;

use Laravel\Spark\Spark;
use Illuminate\Http\Request;
use Laravel\Spark\Invitation;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Contracts\Repositories\TeamRepository;
use Laravel\Spark\Http\Requests\Settings\Teams\CreateInvitationRequest;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\SendInvitation;

class MailedInvitationController extends Controller
{
    /**
     * The team repository implementation.
     *
     * @var \Laravel\Spark\Contracts\Repositories\TeamRepository
     */
    protected $teams;

    /**
     * Create a new controller instance.
     *
     * @param  \Laravel\Spark\Contracts\Repositories\TeamRepository  $teams
     * @return void
     */
    public function __construct(TeamRepository $teams)
    {
        $this->teams = $teams;

        $this->middleware('auth');
    }

    /**
     * Get all of the mailed invitations for the given team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Spark\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request, $team)
    {
        abort_unless($request->user()->onTeam($team), 404);

        return $team->invitations;
    }

    /**
     * Create a new invitation.
     *
     * @param  \Laravel\Spark\Http\Requests\Settings\Teams\CreateInvitationRequest  $request
     * @param  \Laravel\Spark\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function store(CreateInvitationRequest $request, $team)
    {
        Spark::interact(SendInvitation::class, [$team, $request->email, $request->role]);
    }

    /**
     * Cancel / delete the given invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Spark\Invitation  $invitation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Invitation $invitation)
    {
        abort_unless($request->user()->ownsTeam($invitation->team), 404);

        $invitation->delete();
    }
}
