<?php

namespace Tests\Unit;

use App\Models\Publication;
use App\Models\SupervisorProfile;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupervisorProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function supervisor_profile_belongs_to_a_supervisor()
    {
        $teacher = create(User::class);

        $supervisorProfile = create(SupervisorProfile::class, 1, [
            'supervisor_id' => $teacher->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $supervisorProfile->supervisor());
        $this->assertTrue($supervisorProfile->supervisor->is($teacher));
    }

    /** @test */
    public function supervisor_profile_has_many_publications()
    {
        $supervisorProfile = create(SupervisorProfile::class);

        $this->assertInstanceOf(MorphMany::class, $supervisorProfile->publications());

        $this->assertCount(0, $supervisorProfile->publications);

        $publication = create(Publication::class, 1, [
            'main_author_type' => SupervisorProfile::class,
            'main_author_id' => $supervisorProfile->id,
        ]);

        $this->assertCount(1, $supervisorProfile->fresh()->publications);
    }
}
