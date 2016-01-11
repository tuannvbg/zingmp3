<?php

require_once 'autoload.php';

$app = App::create();

$params = $_REQUEST;
$text = isset($params['text']) ? $params['text'] : '';
$userName = isset($params['user_name']) ? $params['user_name'] : '';

if ($userName == 'slackbot') die;

$remote = App::getMopidy();

$responseText = '';

if ($text != '' && strpos($text, BOT_PREFIX) !== 0) {
    switch (strtolower($text)) {
        case 'help':
        case '?':
            $responseText = "" .
                "- Info: `?`, `intro`, `status`, `wake up`, `sleep`\n" .
                "- Tracklist: `current`, `list`\n" .
                "- Playback: `next`, `play`, `stop`, `pause`, `resume`\n" .
                "- Supported links từ *ZingMP3*, *Nhaccuatui*. Site khác không quan tâm\n" .
                "    Format: song, album, playlist (public hoặc cá nhân) \n" .
                "    Ví dụ: \n" .
                "    http://mp3.zing.vn/bai-hat/Minh-Yeu-Tu-Bao-Gio-Em-La-Ba-Noi-Cua-Anh-OST-Miu-Le/ZW7WFEIW.html";
            break;
        case 'intro':
            $responseText = "" .
                "*SlackPi* là tool giúp nghe nhạc theo yêu cầu\n" .
                "và được tạo bởi 1 tên lười lết mông tới chỗ Ampli để chuyển bài hát \n" .
                "Đối tượng sử dụng: Thích nghe nhạc và độ lười trên trung bình \n\n" .
                "Tự mở từ 8h30-9h30 a.m, 4h30-10h p.m hàng ngày trừ T7,CN\n\n" .
                "500đ document: <http://docs.tiki.com.vn/display/TECH/PiMusic> \n" .
                "Code: <https://github.com/tungbi/slackpi> \n" .
                "Anh em sửa, Pull Request thoải mái";
            break;
        case 'next':
            $remote->next();
            $responseText = 'Neeeeeeeeeext';
            break;
        case 'prev':
        case 'back':
            $remote->previous();
            $responseText = 'Ok!';
            break;
        case 'play':
            $remote->play();
            $responseText = 'Ok!';
            break;
        case 'pause':
            $remote->pause();
            $responseText = 'Ok!';
            break;
        case 'resume':
            $remote->resume();
            $responseText = 'Ok!';
            break;
        case 'stop':
            $remote->stop();
            $responseText = 'Shhh!';
            break;
        case 'clear':
            $remote->clear();
            $responseText = 'Clean & Clear!';
            break;
        case 'current':
        case 'now':
            if (!$remote->isPlaying())
                $responseText = 'Not playing';
            else
                $responseText = 'Current track: ' . $remote->getCurrent();
            break;
        case 'list':
            $tracks = $remote->listTracks();
            $responseText = \App::getSlack()->getTrackList($tracks);
            break;
        case 'ping':
        case 'hey':
        case 'status':
            $isRunning = $remote->getServiceStatus();
            $responseText = ($isRunning) ? "Vẫn sống nhăn :grin:" : ":zzz:";
            break;
        case 'wakeup':
        case 'wake up':
        case 'wake_up':
        case 'wake-up':
            $isRunning = $remote->getServiceStatus();
            if ($isRunning)
                $responseText = "Vẫn tỉnh nãy giờ :unamused:";
            else {
                $remote->startService();
                $isRunning = $remote->getServiceStatus();
                $responseText = ($isRunning) ? "Hế nhô :kissing_smiling_eyes:" : ":zzz:";
            }
            break;
        case 'sleep':
            $remote->stopService();
            $isRunning = $remote->getServiceStatus();
            $responseText = ($isRunning) ? "Ko hiểu sao nhưng vưỡn tỉnh như sáo :flushed:" : ":zzz:";
            break;
        case 'restart':
            $remote->restartService();
            $responseText = "Restarted";
            break;
        default:
            $result = \App::getParser()->match($text);

            if ($result !== FALSE) {
                \App::getQueue()->add('getter', ['url' => $result, 'originData' => $params]);
            }
    }
}


if ($responseText != '') {
    echo json_encode(['text' => $responseText]);
}
