<?php

namespace App\Listeners;

use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Auth\Events\Authenticated;

class AttachPatientOnFirstLogin
{
  /**
   * Handle the event: on first patient login, create Patient record and auto-attach bookings.
   */
  public function handle(Authenticated $event): void
  {
    $user = $event->user;

    // Only for patient role
    if ($user->role !== 'patient') {
      return;
    }

    // If patient record already exists, do nothing
    if ($user->patient) {
      return;
    }

    // Step 1: Find the doctor from any pending appointment booked by this email
    $firstBooking = Appointment::where('booked_by_email', $user->email)
      ->whereNull('patient_id')
      ->whereIn('status', ['pending', 'confirmed'])
      ->first();

    $doctorId = $firstBooking?->doctor_id;

    // Step 2: Create Patient record
    $patient = Patient::create([
      'user_id'     => $user->id,
      'doctor_id'   => $doctorId,
      'program_id'  => null,
      'enrolled_at' => now(),
    ]);

    // Step 3: Auto-attach all pending/confirmed bookings by this email to the patient
    Appointment::where('booked_by_email', $user->email)
      ->whereNull('patient_id')
      ->whereIn('status', ['pending', 'confirmed'])
      ->update(['patient_id' => $patient->id]);
  }
}
