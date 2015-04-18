<?php
require('../config.php');
try {
    $handler = new PDO('mysql:host=localhost;dbname='.$worlddb, $user, $password);
    $handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo $e->getMessage();
    die();
}

function getRealMotherfucker(&$coucou) {
	switch ($coucou) {
		case 1: $coucou = "1"; break;
		case 2: $coucou = "2"; break;
		case 3: $coucou = "3"; break;
		case 4: $coucou = "4"; break;
		default: $coucou = "0";
    }
}

if(!isset($_GET['zone'])) {
	echo '<p>Il faut renseigner une zone avec son nom anglais !<br />Exemple : index.php?zone=nagrand</p>
			<ul>
				<li><a href="?zone=peninsula">Hellfire Peninsula</a></li>
			</ul>';
} else {
	$zone = "%".htmlspecialchars($_GET['zone'])."%";

	$query = $handler->prepare('SELECT id, name FROM dbc.dbc_areatable WHERE name LIKE :zone');
	$query->bindValue(':zone', $zone, PDO::PARAM_INT);
	$query->execute();
	$getZoneID = $query->fetch();

	if($query->rowCount() == 0) {
		echo "Aucune zone n'a <em>". htmlspecialchars($_GET['zone']) ."</em> dans son nom.";
	}
	else {

	// DAT SQL QUERY
	// 3483 = Hellfire Peninsula
	$query = $handler->prepare('SELECT qt.entry, qt.Title, qt.RequiredRaces as race, it.entry as itemid, it.name as itemname,
							  ct.entry as idstarter, ct.name as starter, ct2.entry as idender, ct2.name as ender,
							  qtest.startTxt, qtest.progTxt, qtest.endTxt, qtest.txtEvent, qtest.pathEvent, qtest.timeEvent,
							  qtest.Exp, qtest.Stuff, qtest.Gold, qtest.emotNPC, qtest.spellNPC, qtest.placeNPC, qtest.workObj, qtest.baObj,
                              qtest.other,
							  objstart.id as objidstarter, objt.name as objstarter,
							  objend.id as objidender, objt2.name as objender
							  FROM quest_template qt
							  LEFT JOIN creature_questrelation qstart ON qt.entry = qstart.quest
							  LEFT JOIN creature_involvedrelation qend ON qt.entry = qend.quest
							  LEFT JOIN creature_template ct ON qstart.id = ct.entry
							  LEFT JOIN creature_template ct2 ON qend.id = ct2.entry
							  LEFT JOIN gameobject_questrelation objstart ON qt.entry = objstart.quest
							  LEFT JOIN gameobject_involvedrelation objend ON qt.entry = objend.quest
							  LEFT JOIN gameobject_template objt ON objstart.id = objt.entry
							  LEFT JOIN gameobject_template objt2 ON objend.id = objt.entry
							  LEFT JOIN item_template it ON qt.entry = it.startquest
							  LEFT JOIN suntools.quest_test qtest ON qt.entry = qtest.questid
							  WHERE ZoneOrSort = :zone AND qt.Title NOT LIKE "%BETA%"');
	$query->bindValue(':zone', $getZoneID['id'], PDO::PARAM_INT);
	$query->execute();
	?>
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
			<title>SunQuest :: <?php echo $getZoneID['name']; ?></title>
			<link rel="stylesheet" href="css/jquery.dataTables.css">
			<link rel="stylesheet" href="main.css">
			<link rel="stylesheet" href="css/bootstrap.css">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<script src="js/jquery-1.11.2.min.js"></script>
			<script src="js/jquery.dataTables.js"></script>
		</head>
		<body>
			<div class="fluid-container">
				<table id="table" class="table table-hover">
					<thead>
						<tr class="top">
							<td class="small" rowspan="2">A/H</td>
							<td class="med" rowspan="2">Name</td>
							<td class="small" rowspan="2">#</td>
							<td class="small" rowspan="2">Start<br />End</td>
							<td class="med" colspan="3">Textes</td>
							<td class="med" colspan="3">Event</td>
							<td class="med" colspan="3">Gains</td>
							<td class="med" colspan="3">NPCs</td>
							<td class="med" colspan="2">Objets</td>
							<td class="big" rowspan="2">Commentaire</td>
						</tr>
						<tr class="top">
							<td class="part">Start</td>
							<td class="part">Prog.</td>
							<td class="part border2">End</td>
							<td class="part">Txts</td>
							<td class="part">Path</td>
							<td class="part border2">Time</td>
							<td class="part">Exp</td>
							<td class="part">Stuff</td>
							<td class="part border2">Gold</td>
							<td class="part">Emot</td>
							<td class="part">Spells</td>
							<td class="part border2">Place</td>
							<td class="small">Working</td>
							<td class="small">BA ?</td>
						</tr>
					</thead>
					<tbody>
	<?php 
		while ($quests = $query->fetch()) {	

			if ($quests['starter'] == null AND $quests['itemid'] == null) {
				$quest_start = '<img src="quest_start.gif" alt="Quest Start" title="' . $quests['objstarter'] .'" />';
			} elseif ($quests['objstarter'] == null AND $quests['starter'] == null) {
				$quest_start = '<a href="http://www.wowhead.com/item=' . $quests['itemid'] . '"><img src="quest_start.gif" alt="Quest Start" title="' . $quests['itemname'] .'" /></a>';
			} else {
				$quest_start = '<a href="http://www.wowhead.com/npc=' . $quests['idstarter'] . '"><img src="quest_start.gif" alt="Quest Start" title="' . $quests['starter'] .'" /></a>';
			}

			if ($quests['ender'] == null) {
				$quest_end = '<a href="http://www.wowhead.com/item=' . $quests['objidender'] . '"><img src="quest_end.gif" alt="Quest End" title="' . $quests['objender'] .'" /></a>';
			} else {
				$quest_end = '<a href="http://www.wowhead.com/npc=' . $quests['idender'] . '"><img src="quest_end.gif" alt="Quest End" title="' . $quests['ender'] .'" /></a>';
			}

			getRealMotherfucker($quests['startTxt']);
			getRealMotherfucker($quests['progTxt']);
			getRealMotherfucker($quests['endTxt']);
			getRealMotherfucker($quests['txtEvent']);
			getRealMotherfucker($quests['pathEvent']);
			getRealMotherfucker($quests['timeEvent']);
			getRealMotherfucker($quests['Exp']);
			getRealMotherfucker($quests['Stuff']);
			getRealMotherfucker($quests['Gold']);
			getRealMotherfucker($quests['emotNPC']);
			getRealMotherfucker($quests['spellNPC']);
			getRealMotherfucker($quests['placeNPC']);
			getRealMotherfucker($quests['workObj']);
			getRealMotherfucker($quests['baObj']);

			switch($quests['race']) {
				case 0: $race = ""; break;
				case 690: $race = "<img src='horde.png' alt='Horde' /><span>Horde</span>"; break;
				case 1101: $race = "<img src='alliance.png' alt='Alliance' /><span>Alliance</span>"; break;
				default: $race = "";
			}
	?>
						<tr>
							<td>
								<?php echo $race; ?>
							</td>
							<td>
								<a href="http://www.wowhead.com/quest=<?php echo $quests['entry']; ?>"><?php echo $quests['Title']; ?></a>
							</td>
							<td>
								<?php echo $quests['entry']; ?>
							</td>
							<td class="border">
								<?php echo $quest_start . $quest_end; ?>
							</td>
							<td status="<?php echo $quests['startTxt']; ?>" id="<?php echo $quests['entry']; ?>_1" onclick='testQuest(<?php echo $quests['entry']; ?>, 1)' class="test"></td>
							<td status="<?php echo $quests['progTxt']; ?>" id="<?php echo $quests['entry']; ?>_2" onclick='testQuest(<?php echo $quests['entry']; ?>, 2)' class="test"></td>
							<td status="<?php echo $quests['endTxt']; ?>" id="<?php echo $quests['entry']; ?>_3" onclick='testQuest(<?php echo $quests['entry']; ?>, 3)' class="test border"></td>
							<td status="<?php echo $quests['txtEvent']; ?>" id="<?php echo $quests['entry']; ?>_4" onclick='testQuest(<?php echo $quests['entry']; ?>, 4)' class="test"></td>
							<td status="<?php echo $quests['pathEvent']; ?>" id="<?php echo $quests['entry']; ?>_5" onclick='testQuest(<?php echo $quests['entry']; ?>, 5)' class="test"></td>
							<td status="<?php echo $quests['timeEvent']; ?>" id="<?php echo $quests['entry']; ?>_6" onclick='testQuest(<?php echo $quests['entry']; ?>, 6)' class="test border"></td>
							<td status="<?php echo $quests['Exp']; ?>" id="<?php echo $quests['entry']; ?>_7" onclick='testQuest(<?php echo $quests['entry']; ?>, 7)' class="test"></td>
							<td status="<?php echo $quests['Stuff']; ?>" id="<?php echo $quests['entry']; ?>_8" onclick='testQuest(<?php echo $quests['entry']; ?>, 8)' class="test"></td>
							<td status="<?php echo $quests['Gold']; ?>" id="<?php echo $quests['entry']; ?>_9" onclick='testQuest(<?php echo $quests['entry']; ?>, 9)' class="test border"></td>
							<td status="<?php echo $quests['Stuff']; ?>" id="<?php echo $quests['entry']; ?>_10" onclick='testQuest(<?php echo $quests['entry']; ?>, 10)' class="test"></td>
							<td status="<?php echo $quests['spellNPC']; ?>" id="<?php echo $quests['entry']; ?>_11" onclick='testQuest(<?php echo $quests['entry']; ?>, 11)' class="test"></td>
							<td status="<?php echo $quests['placeNPC']; ?>" id="<?php echo $quests['entry']; ?>_12" onclick='testQuest(<?php echo $quests['entry']; ?>, 12)' class="test border"></td>
							<td status="<?php echo $quests['workObj']; ?>" id="<?php echo $quests['entry']; ?>_13" onclick='testQuest(<?php echo $quests['entry']; ?>, 13)' class="test"></td>
							<td status="<?php echo $quests['baObj']; ?>" id="<?php echo $quests['entry']; ?>_14" onclick='testQuest(<?php echo $quests['entry']; ?>, 14)' class="test border"></td>
							<td id="<?php echo $quests['entry']; ?>_15" ><textarea id="<?php echo $quests['entry']; ?>"><?php echo $quests['other']; ?></textarea></td>
						</tr>
	<?php
										  }
	?>
					</tbody>
				</table>
			</div>
			<script type="text/javascript">
				$(document).ready(function() {
					$('#table').dataTable( {
						"scrollY":        "calc(100vh - 106px)",
						"scrollCollapse": false,
						"paging":         false,
						"order": [[ 2, "asc" ]]
					} );

					//set the celles visuals based on their status
					 $('tbody td').each(function(lol, element) {
						 var status = $(element).attr("status");
						 if(status !== undefined) { //jQuery attr() returns undefined on no result
							setElementColorByStatus(element, status);
						 }
					 });
				} );

				function setElementColorByStatus(element, status) {
					$(element).toggleClass('ok', false);
					$(element).toggleClass('mid', false);
					$(element).toggleClass('ko', false);
					$(element).toggleClass('no', false);

					switch(parseInt(status))
					{
						case 0:	break;
						case 1:	$(element).toggleClass('ok',true);	break;
						case 2:	$(element).toggleClass('mid',true); break;
						case 3:	$(element).toggleClass('ko',true);	break;
						case 4:	$(element).toggleClass('no',true);	break;
					}
				}

				function testQuest(questId, column) {
					var element = $('#'+questId+"_"+column);

					var status = element.attr('status');
					status = (parseInt(status) + 1)%5;
					element.attr('status',status);

					setElementColorByStatus(element, status);

					var UrlToPass = 'questId='+questId+'&column='+column+'&status='+status;
						$.ajax({
						type : 'GET',
						data : UrlToPass,
						url  : 'quest.php'
						});
				} 
                
                
                $('textarea').change(function(){
                    var questId = $(this).attr('id');
                    var Comment = $(this).val();
                    Comment = Comment.replace(/\r?\n/g, '%0D%0A');
                    var UrlToPass = 'questId='+questId+'&comment='+Comment;
                        $.ajax({
                        type : 'GET',
                        data : UrlToPass,
                        url  : 'quest.php'
                        });
                });
			</script>
		</body>
	</html>
<?php
	}
}
?>
