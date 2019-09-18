<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\OutgoingLetter;
use App\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class UpdateOutgoingLettersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_update_letters()
    {
        $letter = factory(OutgoingLetter::class)->create();
        $this->withExceptionHandling()
            ->patch("/outgoing-letters/{$letter->id}", ['date' => '2018-08-9'])
            ->assertRedirect('/login');
            
        $this->assertEquals($letter->date, $letter->fresh()->date);
    }

    /** @test */
    public function user_can_update_outgoing_letter_in_database()
    {
        $this->be($user = factory(User::class)->create());
        $sender = factory(User::class)->create();
        $letter = factory(OutgoingLetter::class)->create();
        $new_outgoing_letter = [
            'date' =>  "1987-02-08",
            'type' =>  "et facilis deserunt",
            'recipient' =>  "Raleigh Wunsch",
            'sender_id' =>  $sender->id,
            'description' =>  "Voluptatem est odit voluptas eius deserunt. Nihil nostrum cum sunt a dolores voluptatibus assumenda. Magni inventore quae sed sequi magni voluptatem voluptate. Illo tenetur magnam laboriosam nihil.",
            'amount' =>  11243.56
        ];

        $this->withoutExceptionHandling()
            ->patch(
                "/outgoing-letters/{$letter->id}",
                $new_outgoing_letter
            )->assertRedirect('/outgoing-letters');
       
        $letter = $letter->fresh();
        $this->assertEquals($new_outgoing_letter['type'], $letter->type);
        $this->assertEquals($new_outgoing_letter['description'], $letter->description);
        $this->assertEquals($new_outgoing_letter['sender_id'], $letter->sender_id);
        $this->assertEquals($new_outgoing_letter['amount'], $letter->amount);
        $this->assertEquals($new_outgoing_letter['recipient'], $letter->recipient);
        $this->assertEquals($new_outgoing_letter['date'], $letter->date->format('Y-m-d'));
    }

    /** @test */
    public function user_can_not_update_creator_id_of_an_outgoing_letter_in_database()
    {
        $this->be($user = factory(User::class)->create());
        $letter = factory(OutgoingLetter::class)->create();
        $new_outgoing_letter = factory(OutgoingLetter::class)->make();

        $this->withoutExceptionHandling()
            ->patch(
                "/outgoing-letters/{$letter->id}",
                $new_outgoing_letter->toArray()
            )->assertRedirect('/outgoing-letters');
            
        $this->assertEquals($letter->creator_id, $letter->fresh()->creator_id);
        
    }

    /** @test */
    public function request_validates_date_field_cannot_be_null()
    {
        try{
            $letter = factory(OutgoingLetter::class)->create();
            $this->be(factory(\App\User::class)->create());
            $this->withoutExceptionHandling()
                ->patch("/outgoing-letters/{$letter->id}", ['date'=>'']);
        }catch(ValidationException $e){
            $this->assertArrayHasKey('date', $e->errors());
        }
        
        $this->assertEquals($letter->date,$letter->fresh()->date);
    }

    /** @test */
    public function request_validates_date_field_is_a_valid_date()
    {
        $letter = factory(OutgoingLetter::class)->create();
        $this->be(factory(\App\User::class)->create());

        $invalidDates = [
            '2014-16-14', //16 is not a valid month
            '2017-02-29', //not a leap year
            '2017-04-31', //31 date does not exist in 4th month
            '04-2017-12', // wrong format
        ];

        $validDates = [
            '2018-01-31',
            '2016-02-29',
            '2018-02-28',
            '2018-03-30',
            
        ];

        foreach ($invalidDates as $date) {
            try {
                $this->withoutExceptionHandling()
                    ->patch("/outgoing-letters/{$letter->id}", ['date'=>$date]);
                        
                $this->fail("Invalid date '{$date}' was not validated");
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('date', $e->errors());
                $this->assertEquals($letter->date, $letter->fresh()->date);
            } catch (\Exception $e) {
                $this->fail("Invalid date '{$date}' was not validated");
            }
        }

        foreach ($validDates as $date) {
            $this->withoutExceptionHandling()
                ->patch("/outgoing-letters/{$letter->id}", ['date'=>$date,])
                ->assertRedirect('/outgoing-letters');

            $this->assertEquals($date, $letter->fresh()->date->format('Y-m-d'));
        }
    }

    /** @test */
    public function request_validates_date_field_cannot_be_a_future_date()
    {
        $letter = factory(OutgoingLetter::class)->create();
        $this->be(factory(\App\User::class)->create());

        try {
            $this->withoutExceptionHandling()
                ->patch("/outgoing-letters/{$letter->id}", ['date' => $date = now()->addMonth(1)->format('Y-m-d')]);

            $this->fail("Future date '{$date}' was not validated");
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('date', $e->errors());
            $this->assertEquals($letter->date, $letter->fresh()->date);
        } catch (\Exception $e) {
            $this->fail("Future date '{$date}' was not validated");
        }

        $date = now()->subMonth(1)->format('Y-m-d');
        $this->withoutExceptionHandling()
            ->patch("/outgoing-letters/{$letter->id}", ['date'=>$date])
                ->assertRedirect('/outgoing-letters');

        $this->assertEquals($date, $letter->fresh()->date->format('Y-m-d'));
    }

    /** @test */
    public function request_validates_sender_id_field_cannot_be_null()
    {
        try{
            $letter = factory(OutgoingLetter::class)->create();
            $this->be(factory(\App\User::class)->create());
            $this->withoutExceptionHandling()
                ->patch("/outgoing-letters/{$letter->id}", ['sender_id'=>'']);
        }catch(ValidationException $e){
            $this->assertArrayHasKey('sender_id', $e->errors());
        }
        
        $this->assertEquals($letter->sender_id,$letter->fresh()->sender_id);
    }

    /** @test */
    public function request_validates_sender_id_field_must_be_a_existing_user()
    {
        $letter = factory(OutgoingLetter::class)->create();
        try {
            $this->be(factory(\App\User::class)->create());
        
            $this->withoutExceptionHandling()
                ->patch("/outgoing-letters/{$letter->id}", ['sender_id' => 4]);
            
            $this->fail('Failed to validate \'sender_id\' is a valid existing user id');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('sender_id', $e->errors());
        }
        $this->assertEquals($letter->sender_id, $letter->fresh()->sender_id);
    }

    /** @test */
    public function request_validates_description_field_can_be_null()
    {
        $letter = factory(OutgoingLetter::class)->create();
        $this->be(factory(\App\User::class)->create());
    
        $this->withoutExceptionHandling()
            ->patch("/outgoing-letters/{$letter->id}", ['description' => ''])
            ->assertRedirect('/outgoing-letters');

        $this->assertNull($letter->fresh()->description);
    }

     /** @test */
     public function request_validates_description_field_maxlimit_400()
     {
         $letter = factory(OutgoingLetter::class)->create();
         $this->be(factory(\App\User::class)->create());
        
         try{
            $this->withoutExceptionHandling()
                ->patch("/outgoing-letters/{$letter->id}", ['description' => Str::random(401)])
                ->assertRedirect('/outgoing-letters');
         }catch(ValidationException $e){
             $this->assertArrayHasKey('description',$e->errors());
         }
 
         $this->assertEquals($letter->description,$letter->fresh()->description);
     }

    /** @test */
    public function request_validates_amount_field_can_be_null()
    {
        $letter = factory(OutgoingLetter::class)->create();
        $this->be(factory(\App\User::class)->create());
        $this->withoutExceptionHandling()
             ->patch("/outgoing-letters/{$letter->id}", ['amount'=>''])
             ->assertRedirect('/outgoing-letters');
        $this->assertNull($letter->fresh()->amount);
    }

    /** @test */
    public function request_validates_amount_field_can_not_be_a_string_value()
    {
        $letter = factory(OutgoingLetter::class)->create();
        try {
            $this->be(factory(\App\User::class)->create());
                
            $this->withoutExceptionHandling()
                ->patch("/outgoing-letters/{$letter->id}", ['amount' => 'some string']);
                    
            $this->fail('Failed to validate \'amount\' cannot be a string value');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('amount', $e->errors());
            $this->assertEquals($letter->amount, $letter->fresh()->amount);
        }
    }

    /** @test */
    public function request_validates_type_field_cannot_be_null()
    {
        try{
            $letter = factory(OutgoingLetter::class)->create();
            $this->be(factory(\App\User::class)->create());
            $this->withoutExceptionHandling()
                ->patch("/outgoing-letters/{$letter->id}", ['type'=>'']);
        }catch(ValidationException $e){
            $this->assertArrayHasKey('type', $e->errors());
        }
        
        $this->assertEquals($letter->type,$letter->fresh()->type);
    }

    /** @test */
    public function request_validates_recipient_field_cannot_be_null()
    {
        try{
            $letter = factory(OutgoingLetter::class)->create();
            $this->be(factory(\App\User::class)->create());
            $this->withoutExceptionHandling()
                ->patch("/outgoing-letters/{$letter->id}", ['recipient'=>'']);
        }catch(ValidationException $e){
            $this->assertArrayHasKey('recipient', $e->errors());
        }
        
        $this->assertEquals($letter->recipient,$letter->fresh()->recipient);
    }

}
