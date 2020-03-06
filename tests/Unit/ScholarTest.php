<?php

namespace Tests\Unit;

use App\Scholar;
use App\SupervisorProfile;
use App\Teacher;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ScholarTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function advisorFields($count = 1, $overrides = [])
    {
        $advisors = [];

        for ($x = 0; $x < $count; $x++) {
            $advisor = [
                'title' => $this->faker->title,
                'name' => $this->faker->name,
                'designation' => $this->faker->jobTitle,
                'affiliation' => $this->faker->company,
                'type' => $this->faker->randomElement(['A', 'C']),
            ];

            $advisor = array_merge($advisor, $overrides);
            array_push($advisors, $advisor);
        }

        return $advisors;
    }

    /** @test */
    public function scholar_may_have_many_advisors()
    {
        $scholar = create(Scholar::class);

        $this->assertInstanceOf(HasMany::class, $scholar->advisors());
        $this->assertCount(0, $scholar->advisors);

        $advisorFieldsArray = $this->advisorFields(3);
        $advisors = $scholar->advisors()->createMany($advisorFieldsArray);

        $this->assertCount(3, $scholar->fresh()->advisors);
        $this->assertTrue($advisors[0]->is($scholar->fresh()->advisors->first()));
    }

    /** @test */
    public function scholar_may_have_an_advisory_committe()
    {
        $scholar = create(Scholar::class);

        $this->assertEquals(0, $scholar->advisoryCommittee->count());

        $advisoryCommitteeFieldsArray = $this->advisorFields(3, ['type' => 'A']);
        $advisoryCommittee = $scholar->advisors()->createMany($advisoryCommitteeFieldsArray);

        $this->assertCount(3, $scholar->fresh()->advisoryCommittee);
        $this->assertTrue($advisoryCommittee[0]->is($scholar->fresh()->advisoryCommittee->first()));
    }

    /** @test */
    public function scholar_may_have_co_supervisors()
    {
        $scholar = create(Scholar::class);

        $this->assertEquals(0, $scholar->advisoryCommittee->count());

        $coSupervisorsFieldsArray = $this->advisorFields(3, ['type' => 'C']);
        $coSupervisors = $scholar->advisors()->createMany($coSupervisorsFieldsArray);

        $this->assertCount(3, $scholar->fresh()->coSupervisors);
        $this->assertTrue($coSupervisors[0]->is($scholar->fresh()->coSupervisors->first()));
    }
}