<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DonationNotifiable extends Model
{
    use Notifiable;

    protected $table = 'contributions'; // Use contributions table
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [];

    private $contributionData;

    public static function forContribution(Contribution $contribution): self
    {
        $instance = new self();
        $instance->contributionData = $contribution;
        $instance->id = $contribution->id;
        return $instance;
    }

    public function getContribution(): Contribution
    {
        if ($this->contributionData) {
            return $this->contributionData;
        }

        return Contribution::with('donationLink')->find($this->id);
    }

    public function routeNotificationForMail()
    {
        return $this->getContribution()->email;
    }

    public function routeNotificationForSms()
    {
        return $this->getContribution()->phone;
    }

    public function routeNotificationForWhatsapp()
    {
        return $this->getContribution()->phone;
    }
}
