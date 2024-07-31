<?php

namespace App\Http\Controllers;

use App\Models\UserWater;
use Illuminate\Http\Request;
use Telegram\Bot\Keyboard\Keyboard;
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
            \Log::info($data);
            if ($chatId && $text) {
                $this->handleMessage($chatId, $text, $messageId);
            }
            if ($chatId && $data) {
                $this->handleCallbackQuery($chatId, $data, $messageId);
            }
            if ($chatId && $contact) {

                $user = UserWater::where('state','await_phone')->first();
                if($user){
                    $this->savePhone($chatId,$contact,false,$messageId, $user);
                }else{
                    $this->handleMessage($chatId,'/start',$messageId);
                }
            }
        }
    }
    public function handleMessage($chatId, $text, $messageId)
    {
        $user = UserWater::where('telegram_id', $chatId)->first();
        if ($user) {
            // botga qayta start bosib yuborsa
            if ($text == '/start') {
                switch ($user->state) {
                    case 'await_phone':
                        $this->start($chatId, $messageId, $user);
                        break;
                    case 'await_order':
                        $this->savePhone($chatId, false, false, $messageId, $user);
                        break;
                //     case 'await_region':
                //         $this->savePhone($chatId, false, $messageId);
                //         break;
                //     case 'await_product':
                //         $this->saveRegion($chatId, $user->region_id, false, $messageId);
                //         break;
                //     case 'await_code':
                //         $this->Code($chatId, $text, $user, $messageId);
                //         break;
                //     case 'finish':
                //         $this->finish($chatId, $user, $messageId);
                //         break;
                }
            }

            if ($text != '/start') {
                switch ($user->state) {
                    case 'await_phone':
                        $this->savePhone($chatId, false, $text, $messageId, $user);
                    break;
                    // case 'await_order':
                    //     $this->saveOrder($chatId, $text, $messageId, $user);
                    // break;
                }
            }
        } else {
            switch ($text) {
                case '/start':
                    $this->start($chatId,$messageId, false);
                    break;
            }
        }
    }

    public function handleCallbackQuery($chatId, $data, $messageId)
    {
        $user = UserWater::where('telegram_id', $chatId)->first();
        if ($data == 'order') {
            $this->sendOrder($chatId,$data, $messageId);
        }
    }
    public function start($chatId,$messageId, $user)
    {
        $text = "Assalomu alaykum uzuuun tanishuv teksti";
        $this->sendMessage($chatId, $text, $messageId);
        $btn = [[['text' => '☎️Telefon raqamni yuborish📲', 'request_contact' => true]]];
        $btnName = 'keyboard';
        $message = 'Suvga buyurtma berish uchun
"📱 Telefon raqamni yuborish" tugmasini bosing 👇
Yoki raqamingizni kiriting (masalan: +998931234567):';
        $this->sendMessageBtn($chatId,$message, $btn, $btnName, $messageId);

    }

    public function savePhone($chatId,$contact,$text,$messageId,$user)
    {
        if ($contact) {
            $phone = "+".substr($contact['phone_number'], -12);
            if(preg_match("/^[+][0-9]+$/", $phone) && strlen($phone) == 13){
                $user->update([
                    'phone' => $phone,
                    'state' => 'await_order',
                ]);
            }else{
               return Telegram::sendMessage([
                    'chat_id'=>$chatId,
                    'text'=>'Sizning raqamingiz mahalliy raqam emas,
📱 bog\'lanish mumkin bo\'lgan
raqamingizni yuboring (masalan: +998931234567):',
                ]);
            }
        }

       if($text){
        $text = "+".substr($text, -12);
        if(preg_match("/^[+][0-9]+$/", $text) && strlen($text) == 13){
            $user->update([
                'phone' => $text,
                'state' => 'await_order',
            ]);
        }else{
            return Telegram::sendMessage([
                'chat_id'=>$chatId,
                'text'=>'📱 O`z telefon raqamingizni yuboring (masalan: +998931234567):',
            ]);
        }
       }
        $remove = Keyboard::make()->setRemoveKeyboard(true);
        Telegram::sendMessage([
            'chat_id'=>$chatId,
            'text'=>'Telefon raqamingiz muvaffaqiyatli saqlandi ✅',
            'reply_markup' => $remove
        ]);
        $message = "Xayrli kun
        Men sizning shaxsiy yordamchi botingizman.
        Mening yordamim bilan siz o'zingizga juda ko'p yaxshi va toza suvga buyurtma berishingiz mumkin 💧
        Yoki mahsulotlarimizni ko'ring📃 👇👇";
        $btn = [
            [['text' => 'Buyurtma berish 👈', 'callback_data' =>'order']],
            [['text'=> 'Biz haqimizda 👈', 'callback_data'=>'about']]
        ];
        $btnName = 'inline_keyboard';
        $this->sendMessageBtn($chatId, $message, $btn, $btnName, $messageId);
    }

    public function sendOrder($chatId, $data, $messageId)
    {
        $message = "Buyurtmangizni sonini kiriting 📃 👈";
        // $user->update([
        //     'state' => 'await_code',
        // ]);
        $this->sendMessage($chatId, $message, $messageId);
    }
    public function sendMessage($chatId, $text, $messageId)
    {
        $user = UserWater::where('telegram_id', $chatId)->first();
        if (!$user) {
            UserWater::create([
                'telegram_id' => $chatId,
                'state' => 'await_phone',
            ]);
        }
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'html'
            ]);
        \Log::info('Telegram response: ' . json_encode($response));
    }

    public function sendMessageBtn($chatId, $text, $btn, $btnName, $messageId)
    {

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
        \Log::info('Telegram response: ' . json_encode($response));
    }
}
