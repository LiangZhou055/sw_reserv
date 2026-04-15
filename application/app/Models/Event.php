<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Event extends Model
{
    use HasFactory;
    
    const STATUS_WAITING = 1;
    const STATUS_DINE = 2;
    const STATUS_CANCEL = 3;
    const STATUS_DELETE = 4;

    const SMS_STATUS_IDLE = 0;
    const SMS_STATUS_SENT = 1;
    const SMS_STATUS_CANCEL = 2;
    const SMS_STATUS_CONFIRM = 3;
    const SMS_STATUS_NOTICED = 4;
    const SMS_STATUS_CANCELED = 5;
    const SMS_STATUS_VOID = 6;

    const SOURCE_STORE = 1;
    const SOURCE_CUSTOMER = 2;
  
    protected $fillable = [
        'title', 'start', 'end',
        'contact_no',
        'email',
        'person_no',
        'order_no',
        'status',
        'sms_status',
        'source',
        'comments',
        'time',
        'sms_time_welcome',
        'sms_time_cancel',
        'sms_time_notice',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_WAITING => 'Waiting',
            self::STATUS_DINE => 'Dine In',
            self::STATUS_CANCEL => 'Canceled',
            self::STATUS_DELETE => 'Deleted',
        ];
    }

    public static function smsStatusLabels(): array
    {
        return [
            self::SMS_STATUS_IDLE => 'Idle',
            self::SMS_STATUS_SENT => 'Sent',
            self::SMS_STATUS_CANCEL => 'Cancel',
            self::SMS_STATUS_CONFIRM => 'Confirm',
            self::SMS_STATUS_NOTICED => 'Noticed',
            self::SMS_STATUS_CANCELED => 'Canceled',
            self::SMS_STATUS_VOID => 'Void',
        ];
    }

    public static function statusLabel($value): string
    {
        $labels = self::statusLabels();
        return $labels[(int) $value] ?? (string) $value;
    }

    public static function smsStatusLabel($value): string
    {
        $labels = self::smsStatusLabels();
        return $labels[(int) $value] ?? (string) $value;
    }

    public static function sourceLabels(): array
    {
        return [
            self::SOURCE_STORE => 'Store',
            self::SOURCE_CUSTOMER => 'Customer',
        ];
    }

    public static function sourceLabel($value): string
    {
        $labels = self::sourceLabels();
        return $labels[(int) $value] ?? (string) $value;
    }
}