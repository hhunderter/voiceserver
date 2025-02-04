<?php $voiceServer = $this->get('voiceServer'); ?>

<?php function getVoiceserverBoxView($items) { ?>
    <?php if(is_array($items) && count($items) > 0): ?>
        <?php $last_channel = array_keys($items)[count($items)-1]; ?>
        <?php foreach ($items as $key => $item): ?>
            <li <?php if($last_channel == $key): ?>class="last"<?php endif; ?> >
                <a href="<?=$item['link'] ?>" title="<?=$item['topic'] ?>" >
                    <?=$item['icon'] . $item['name'] ?>
                    <?php if (isset($item['flags'])): ?>
                        <div class="voiceSrvFlags"><?=$item['flags'] ?></div>
                    <?php endif; ?>
                </a>
                <?php if (isset($item['users'])): ?>
                    <ul>
                        <?php $last_user = array_keys($item['users'])[count($item['users'])-1]; ?>
                        <?php foreach ($item['users'] as $u_key => $user): ?>
                            <li <?php if (!isset($item['children']) && $last_user == $u_key): ?>class="last"<?php endif; ?> >
                                <?=$user['icon'] . $user['name'] ?>
                                <?php if (isset($user['flags'])): ?>
                                    <div class="voiceSrvFlags"><?=$user['flags'] ?></div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>                
                <?php if (isset($item['children'])): ?>
                    <ul>
                        <?php getVoiceserverBoxView($item['children']); ?>  
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
<?php }; ?>

<?php
switch ($voiceServer['Type']) {
    case 'TS3':
        require_once("./application/modules/voiceserver/classes/ts3.php");

        $ts3viewer = new TS3($voiceServer['IP'], $voiceServer['QPort']);
        $ts3viewer->useServerPort($voiceServer['Port']);
        $ts3viewer->hideEmptyChannels = isset($voiceServer['HideEmpty'])?$voiceServer['HideEmpty']:false;
        $ts3viewer->showIcons = isset($voiceServer['CIcons'])?$voiceServer['CIcons']:false;

        $datas = $ts3viewer->getChannelTree(); 
        break;
    case 'Mumble':
        require_once("./application/modules/voiceserver/classes/mumble.php");

        $mumbleviewer = new Mumble('127.0.0.1','6502');
        $mumbleviewer->hideEmptyChannels = isset($voiceServer['HideEmpty'])?$voiceServer['HideEmpty']:false;
        
        $datas = $mumbleviewer->getChannelTree();
        break;
    case 'Ventrilo':
        require_once("./application/modules/voiceserver/classes/ventrilo.php");

        $ventriloviewer = new Ventrilo($voiceServer['IP'], $voiceServer['Port']);
        $ventriloviewer->hideEmptyChannels = isset($voiceServer['HideEmpty'])?$voiceServer['HideEmpty']:false;
        
        $datas = $ventriloviewer->getChannelTree(); 
        break;
    default:
        break;
}
?>

<?php if (is_array($datas)): ?>
    <div class="voiceSrv">
        <ul class="voiceSrvItem voiceSrvServer">
            <a href="<?=$datas['root']['link'] ?>">
                <?=$datas['root']['icon'] . $datas['root']['name'] ?>
            </a>
            <?php getVoiceserverBoxView($datas['tree']); ?>
        </ul>
    </div>
<?php endif; ?>
