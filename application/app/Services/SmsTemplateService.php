<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SmsTemplateService
{
    public const TYPE_WELCOME = 'welcome';
    public const TYPE_NOTICE = 'notice';
    public const TYPE_CONFIRM = 'confirm';
    public const TYPE_CANCEL = 'cancel';

    public function render(string $type, ?Event $event = null): string
    {
        $template = $this->resolveTemplate($type);
        $vars = $this->buildVariables($event);
        return strtr($template, $vars);
    }

    private function resolveTemplate(string $type): string
    {
        $columnMap = [
            self::TYPE_WELCOME => 'sms_tpl_welcome',
            self::TYPE_NOTICE => 'sms_tpl_notice',
            self::TYPE_CONFIRM => 'sms_tpl_confirm',
            self::TYPE_CANCEL => 'sms_tpl_cancel',
        ];

        $column = $columnMap[$type] ?? null;
        if ($column !== null && Schema::connection('central')->hasColumn('stores', $column)) {
            $storeCode = app()->bound('store.code') ? (string) app('store.code') : '';
            if ($storeCode !== '') {
                $store = DB::connection('central')
                    ->table('stores')
                    ->whereRaw('LOWER(code) = ?', [strtolower($storeCode)])
                    ->first();
                $value = (string) ($store->{$column} ?? '');
                if (trim($value) !== '') {
                    return $value;
                }
            }
        }

        return $this->fallbackTemplate($type);
    }

    private function fallbackTemplate(string $type): string
    {
        switch ($type) {
            case self::TYPE_NOTICE:
                return 'Votre réservation au {store_name} est le {date} à {time} pour {party_size} personnes, #{order_no}. Répondez {confirm_keyword} pour confirmer, ou {cancel_keyword} pour annuler.';
            case self::TYPE_CONFIRM:
                return 'Votre réservation est confirmée. {store_name}, par Sayweb.ca.';
            case self::TYPE_CANCEL:
                return 'Votre réservation est annulée. {store_name}, par Sayweb.ca';
            case self::TYPE_WELCOME:
            default:
                return 'Votre réservation au {store_name} est le {date} à {time} pour {party_size} personnes, #{order_no}. Votre place sera réservée pour 10 min. par Sayweb.ca';
        }
    }

    private function buildVariables(?Event $event): array
    {
        $storeName = StoreContext::getRestName();
        $orderNo = '';
        $date = '';
        $time = '';
        $partySize = '';

        if ($event !== null) {
            $orderNo = StoreContext::getRestPrefix().$event->order_no;
            $date = Carbon::parse($event->start)->format('m-d');
            $time = substr((string) $event->time, 0, 5);
            $partySize = (string) ((int) $event->person_no);
        }

        return [
            '{store_name}' => $storeName,
            '{order_no}' => $orderNo,
            '{date}' => $date,
            '{time}' => $time,
            '{party_size}' => $partySize,
            '{confirm_keyword}' => '1',
            '{cancel_keyword}' => '2',
        ];
    }
}
