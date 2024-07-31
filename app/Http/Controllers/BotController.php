<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserWater;
use Telegram\Bot\FileUpload\InputFile;
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
            if($chatId){
                if($chatId == -1002186487946){
                    return null;
                }
            }
            if ($chatId && $text) {
                $this->handleMessage($chatId, $text, $messageId);
            }
            if ($chatId && $data) {
                $this->handleCallbackQuery($chatId, $data, $messageId);
            }
            if ($chatId && $contact) {

                $user = UserWater::where('state', 'await_phone')->first();
                if ($user) {
                    $this->savePhone($chatId, $contact, false, $messageId, $user);
                } else {
                    $this->handleMessage($chatId, '/start', $messageId);
                }
            }
        }
    }
    public function handleMessage($chatId, $text, $messageId)
    {
        $user = UserWater::where('telegram_id', $chatId)->first();
        if ($text == '/start') {
            $this->start($chatId, $messageId, $user);
        }
        if ($user) {
            // botga qayta start bosib yuborsa
            if ($text != '/start') {
                switch ($user->state) {
                    case 'await_phone':
                        $this->savePhone($chatId, false, $text, $messageId, $user);
                        break;
                    case 'await_order_quantity':
                        $this->saveOrder($chatId, $text, $messageId, $user);
                        break;
                    case 'await_location':
                        $this->saveLocation($chatId, $text, $messageId, $user);
                        break;
                }
            }
        }
    }

    public function handleCallbackQuery($chatId, $data, $messageId)
    {
        $user = UserWater::where('telegram_id', $chatId)->first();
        if ($data == 'order') {
            $this->sendOrder($chatId, $messageId, $user);
        }
        if ($data == 'new_order') {
            $user->update([
                'state'=>'await_phone'
            ]);
            $this->start($chatId, $messageId, $user);
        }
    }
    public function start($chatId, $messageId, $user)
    {
        if(!$user){
           $userOrder =  UserWater::create([
                'telegram_id' => $chatId,
                'state' => 'await_phone',
            ]);
            Order::create([
                'user_id'=>$userOrder->id
            ]);
            $text = "Assalomu alaykum uzuuun tanishuv teksti";
            $photo = InputFile::create(public_path('bot.jpg'));
            Telegram::sendPhoto([
                'chat_id'=>$chatId,
                'photo'=>$photo,
                'caption'=>$text
            ]);
        }
       if($user){
        $user->update([
            'state'=>'await_phone'
        ]);
        Order::create([
            'user_id'=>$user->id
        ]);
       }
        $btn = [[['text' => '☎️Telefon raqamni yuborish📲', 'request_contact' => true]]];
        $btnName = 'keyboard';
        $message = 'Sizga bog\'lanish uchun
"📱 Telefon raqamni yuborish" tugmasini bosing 👇
Yoki raqamingizni kiriting (masalan: 931234567):';
        $this->sendMessageBtn($chatId, $message, $btn, $btnName, $messageId);
    }

    public function savePhone($chatId, $contact, $text, $messageId, $user)
    {
        if ($contact) {
            $phone = "+" . substr($contact['phone_number'], -12);
            if (preg_match("/^[+][0-9]+$/", $phone) && strlen($phone) == 13) {
                $user->update([
                    'phone' => $phone,
                    'state' => 'await_order',
                ]);
                $user->order->update([
                    'phone'=>$phone
                ]);
            } else {
                return Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Sizning raqamingiz mahalliy raqam emas,
📱 bog\'lanish mumkin bo\'lgan
raqamingizni yuboring (masalan: 931234567):',
                ]);
            }
        }

        if ($text) {
            $text = "+" . substr($text, -12);
            if (preg_match("/^[+][0-9]+$/", $text) && strlen($text) == 13) {
                $user->update([
                    'phone' => $text,
                    'state' => 'await_order',
                ]);
                $user->order->update([
                    'phone'=>$phone
                ]);
            } else {
                return Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '📱 O`z telefon raqamingizni yuboring (masalan: 931234567):',
                ]);
            }
        }
        $remove = Keyboard::make()->setRemoveKeyboard(true);
        $btn = [
            [['text' => 'Buyurtma berish 👈', 'callback_data' => 'order']],
            [['text' => 'Biz haqimizda 👈', 'callback_data' => 'about']],
        ];
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Telefon raqamingiz muvaffaqiyatli saqlandi ✅',
            'reply_markup' =>  json_encode([
                'inline_keyboard' => $btn,
                'remove_keyboard' => true // This will clear any existing custom keyboard
            ])

        ]);
//         $message = "Men sizning shaxsiy yordamchi botingizman.
// Mening yordamim bilan siz o'zingizga juda ko'p yaxshi va
// toza suvga buyurtma berishingiz mumkin 💧
// Yoki mahsulotlarimizni ko'ring📃 👇👇";

//         $btnName = 'inline_keyboard';
//         $this->sendMessageBtn($chatId, $message, $btn, $btnName, $messageId);
    }

    public function sendOrder($chatId, $messageId, $user)
    {
        if ($user) {
            $user->update([
                'state' => 'await_order_quantity',
            ]);
        }
        $message = 'Buyurtmangizni sonini kiriting! 📃 👇';
        $this->sendMessage($chatId, $message, $messageId, $user);
    }

    public function saveOrder($chatId, $text, $messageId, $user)
    {
        if ($user) {
            if (is_numeric($text)) {
                $user->update([
                    'state' => 'await_location',
                ]);
                $user->order->update([
                    'quantity' => $text,
                ]);
            } else {
                $message = 'Buyurtma sonini faqat raqamlar orqali kiriting. (masalan: 2)';
                return $this->sendMessage($chatId, $message, $messageId, $user);
            }
        }
        $message = 'Yetkazib berish qulay bo\'lishi uchun ❗️
Yetkazib berish manzili , va vaqtini yozib keting iltimos ✅';
        $this->sendMessage($chatId, $message, $messageId, $user);
    }
    public function saveLocation($chatId, $text, $messageId, $user)
    {
        if($user){
            $user->update([
                'state'=>'finish'
            ]);
            $user->order->update([
                'location'=>$text,
                'status'=>true
            ]);
        }
        $message = 'Sizning ma\'lumotingiz muvaffaqiyatli saqlandi ✅
Sizga operatorlarimiz aloqaga chiqishadi ☎️';
        $btn = [
            [['text' => 'Yana Buyurtma berish 👈', 'callback_data' => 'new_order']],
            [['text' => 'Biz haqimizda 👈', 'callback_data' => 'about']],
        ];
        $btnName = 'inline_keyboard';
        $this->sendMessageBtn($chatId, $message,$btn, $btnName, $messageId);
        $chanelMessage = "Tel: ".$user->phone."\n"."Miqdori: ".$user->order->quantity."dona"
."\n"."Tavsif: ".$user->order->location;
        $this->sendMessageChanel($chanelMessage);
    }
    public function sendMessage($chatId, $text, $messageId, $user)
    {
        if (!$user) {
            UserWater::create([
                'telegram_id' => $chatId,
                'state' => 'await_phone',
            ]);
        }
        try {
            $response = Telegram::editMessageText([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $text,
                'parse_mode' => 'html',
            ]);
        } catch (\Exception $e) {
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'html',
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
    public function sendMessageChanel($message){
        Telegram::sendMessage([
            'chat_id'=>-1002186487946,
            'text'=>$message,
            'parse_mode'=>'html'
        ]);
    }
}
