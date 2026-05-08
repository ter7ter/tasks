<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Visit stats</title>
    <link href="style.css" rel="stylesheet" />
</head>
<body>
<form class="select-form" action="index.php" method="post">
    <select name="select_url">
        <?php foreach ($urls as $url):?>
            <option value="<?php echo $url['url'];?>" <?=($url['url'] == $select_url ? 'selected' :'');?> ><?php echo $url['url'];?></option>
        <?php endforeach;?>
    </select><input type='submit' value="Выбрать"></form>
<br />
<form class="select-form" action="index.php" method="post">
    <input type ="hidden" name="select_url" value="<?=$select_url?>">
    Показать с <input type="text" name="start_date" value="<?=$start_date?>"> по <input name="end_date" type="text" value="<?=$end_date?>" />
    <input type='submit' value="Показать">
</form>
<br/>
<img src="time_graph.php?url=<?=urlencode($select_url)?>&date_from=<?=urlencode($start_date)?>&date_to=<?=urlencode($end_date)?>" alt="">
<br/>
<img src="city_graph.php?url=<?=urlencode($select_url)?>&date_from=<?=urlencode($start_date)?>&date_to=<?=urlencode($end_date)?>" alt="">
</body>