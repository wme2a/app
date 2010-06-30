<?php 
	if($results) { // builds a simple table from db results ?>
	<table>
	  <tr>
		<th>title</th>
		<th>commentText</th>
		<th>created</th>
		<th>modified</th>
		<th>commentId</th>
		<th>photoId</th>
		<th>userId</th>
	  </tr>
	  <!-- php echo "DATABASE RESULT<br />"?>
	  < php var_dump($result);?/-->
	  <?php foreach($results as $result) {?>
		  <tr>
			<td><?php echo $html->link($result["Comment"]["title"], "/comment/");?></td>
			<td><?php echo $result["Comment"]["comment_text"]; ?></td>
			<td><?php echo $result["Comment"]["created"]; ?></td>
			<td><?php echo $result["Comment"]["modified"]; ?></td>
			<td><?php echo $result["Comment"]["id"]; ?></td>
			<td><?php echo $result["Comment"]["photo_id"]; ?></td>
			<td><?php echo $result["Comment"]["user_id"]; ?></td>
		  </tr>
	  <?php }?>
	</table> 
<?php 
	}
	else 
	{
		header("HTTP/1.0 412 Precondition Failed");
		echo "";
	}
?>