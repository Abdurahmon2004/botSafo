<?php

namespace App\Http\Controllers;

use App\Models\UserWater;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class BotController extends Controller
{
    public function webhook()
    {
        $update = Telegram::getWebhookUpdates();
        if ($update) {
            $chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'] ?? null;
            $text = $update['message']['text'] ?? null;
            $data = $update['callback_query']['data'] ?? null;
            $messageId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'] ?? null;
            $contact = $update['message']['contact'] ?? null;

            if ($chatId && $text) {
                $this->handleMessage($chatId, $text, $messageId);
            }
            if ($chatId && $data) {
                $this->handleCallbackQuery($chatId, $data, $messageId);
            }
            // if ($chatId && $contact) {
            //     $user = UserWater::where('state','await_phone')->first();
            //     if($user){
            //         $this->savePhone($chatId,$contact,$messageId);
            //     }else{
            //         $this->handleMessage($chatId,'/start',$messageId);
            //     }
            // }
        }
    }
    public function handleMessage($chatId, $text, $messageId)
    {
        $user = UserWater::where('telegram_id', $chatId)->first();
        if ($user) {
            // botga qayta start bosib yuborsa
            // if ($text == '/start') {
            //     switch ($user->state) {
            //         case 'await_name':
            //             $this->start($chatId, $messageId, $user);
            //             break;
            //         case 'await_phone':
            //             $this->saveName($chatId, false, $messageId, $user);
            //             break;
            //         case 'await_region':
            //             $this->savePhone($chatId, false, $messageId);
            //             break;
            //         case 'await_product':
            //             $this->saveRegion($chatId, $user->region_id, false, $messageId);
            //             break;
            //         case 'await_code':
            //             $this->Code($chatId, $text, $user, $messageId);
            //             break;
            //         case 'finish':
            //             $this->finish($chatId, $user, $messageId);
            //             break;
            //     }
            // }

        //     if ($text != '/start') {
        //         switch ($user->state) {
        //             case 'await_name':
        //                 $this->saveName($chatId, $text, $messageId, $user);
        //                 break;
        //             case 'await_code':
        //                 $this->codeSave($chatId, $text, $messageId, $user);
        //                 break;
        //         }
        //     }
        // } else {
            switch ($text) {
                case '/start':
                    $this->start($chatId, $messageId, $user);
                    break;
            }
        }
    }

    // public function handleCallbackQuery($chatId, $data, $messageId)
    // {
    //     $user = UserWater::where('telegram_id', $chatId)->first();
    //     if (strpos($data, 'region_') === 0) {
    //         $regionId = str_replace('region_', '', $data);
    //         $this->saveRegion($chatId, $regionId, $user, $messageId);
    //     }
    //     if (strpos($data, 'product_') === 0) {
    //         $productId = str_replace('product_', '', $data);
    //         $this->saveProduct($chatId, $productId, $user, $messageId);
    //     }
    //     if ($data == 'code') {
    //         $this->Code($chatId, $data, $user, $messageId);
    //     }
    // }
    public function start($chatId, $messageId, $user)
    {
        if(!$user){
            UserWater::create([
                'telegram_id'=>$chatId
            ]);
        }
        $text = "SAFO PHARM 2017 yilda tashkil etilgan bo'lib bugungi kunda Andijon viloyatida 6 ta filialiga ega va bu hali boshlanishi. Bizning rejalarimiz butun respublikamiz, MDH davlatlari va dunyoni qamrab olish. Bizning barcha sa'y-harakatlarimiz malakali va fidoyi jamoamizning sa'y-harakatlaridir. Jamoamiz o'sib bormoqda va biz 40 tadan oshdik. Bugungi kunda hech qanday mubolag'asiz aytishimiz mumkinki, har kuni minglab mijozlarga xizmat ko'rsatuvchi SAFO PHARM ishonchli va mashhur brendga aylandi. Mijozlarimizning ishonchi va sadoqati bizning katta yutug'imizdir.
        Biz har kuni o'zgarib, yaxshiroq va yanada yaxshiroq bo'lish uchun faol ishlaymiz. Bizdagi  afzalliklar tufayli biz muvaffaqiyatga erishishda davom etamiz,  bular - iste'molchilar bilan to'g'ridan-to'g'ri muloqot qilishning nostandart usullari, sifatli dori-darmonlarning keng assortimenti, qulay narx siyosati va marketing dasturlari bilan uyg'unlashgan xizmat ko'rsatishning yuqori standartlaridir.";
        $this->sendMessage($chatId, $text, $messageId);
        $btn = [[['text' => 'Yana kod kiritish!', 'request_contact' => true]]];
        $btnName = 'inline_keyboard';
        $this->sendMessageBtn($chatId,$text, $btn, $btnName, $messageId);

    }
    public function sendMessage($chatId, $text, $messageId)
    {
        $user = UserWater::where('telegram_id', $chatId)->first();
        if (!$user) {
            UserWater::create([
                'telegram_id' => $chatId,
                'state' => 'await_name',
            ]);
        }
        try {
            $response = Telegram::editMessageText([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $text,
                'parse_mode' => 'html'
            ]);
        } catch (\Exception $e) {
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'html'
            ]);
        }
        \Log::info('Telegram response: ' . json_encode($response));
    }

    public function sendMessageBtn($chatId, $text, $btn, $btnName, $messageId)
    {
        try {
            $response = Telegram::editMessageText([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $text,
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    $btnName => $btn,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                ]),
            ]);
        } catch (\Exception $e) {
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    $btnName => $btn,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                ]),
            ]);
        }
        \Log::info('Telegram response: ' . json_encode($response));
    }
}
