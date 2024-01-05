<?php
require_once 'resources/includes/lms.inc.php';

lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();



$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'home';
$page[ 'user_role' ] = lmsGetUserRole();
$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Lorem Ipsum!</h1>
	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed pulvinar tortor ut leo feugiat, molestie laoreet eros cursus. Duis condimentum fermentum arcu, a lacinia ligula pretium quis. Maecenas ornare vestibulum tempor. Donec sed nulla tincidunt, venenatis risus varius, fringilla dui. Pellentesque et posuere massa. In et risus ac risus mollis auctor. Fusce rutrum volutpat metus ut sollicitudin. Vivamus dolor magna, porta non volutpat at, elementum at nibh. Donec at diam mi. Nullam quis felis pretium, eleifend diam et, tristique purus.</p>
	<p>Mauris lacus lorem, efficitur non aliquet vitae, finibus quis justo. Nullam bibendum augue at egestas malesuada. Etiam nec tempus sem. Aliquam erat volutpat. Proin scelerisque magna sit amet nulla ullamcorper, at convallis mauris pulvinar. Phasellus viverra lectus sit amet ullamcorper scelerisque. Proin malesuada felis urna. Quisque varius ultrices sollicitudin. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam nisi lacus, porttitor ut vehicula ac, malesuada at magna.	</p>
	<hr />
	<br />

	<h2>Integer ut dui iaculis</h2>
	<p>elementum elit non, commodo mauris. In mattis congue mi, a mattis quam ornare id. Curabitur convallis blandit felis eget ultrices. Curabitur ac velit viverra, luctus neque at, tincidunt nulla. Phasellus imperdiet, felis vitae lobortis vulputate, neque nibh dignissim nisi, at auctor purus mi in mauris. Morbi vestibulum risus at mauris facilisis, ac pellentesque lacus tempor. Phasellus et est non orci finibus consequat nec eget felis.</p>
	<p>In tincidunt metus at dolor egestas tempus. Pellentesque vel ligula sed justo facilisis lacinia. Etiam lobortis elementum risus, ultricies imperdiet eros tempus non. Nullam leo dolor, auctor eu imperdiet eu, sollicitudin id libero. Integer feugiat, dolor a semper scelerisque, nunc risus pharetra ipsum, a accumsan ligula felis sit amet leo. Praesent iaculis eros lectus, ut lobortis nisi lobortis sed. Mauris imperdiet, nisi mattis dictum condimentum, enim diam auctor dui, sed fermentum urna mi quis tortor. In hac habitasse platea dictumst. Curabitur sed enim vel augue pretium iaculis.</p>
	<p>Donec pretium lobortis lacinia. Nam non ex vulputate, bibendum urna in, feugiat risus. Curabitur posuere diam at urna faucibus, eu facilisis quam tincidunt. Praesent eu purus non tortor pharetra tincidunt at a dui. Nunc quis arcu vehicula, porta sapien quis, imperdiet nulla. Cras at tortor arcu. Maecenas at dui enim. Proin velit nisl, convallis eu libero vel, dictum mattis sem. Cras a sollicitudin orci. Vivamus ac eros quis tortor bibendum cursus. Vestibulum fermentum pellentesque ligula, ut semper augue cursus non. Pellentesque aliquam euismod nisi, in pellentesque nibh. Phasellus laoreet, lectus a aliquam varius, leo leo gravida nisl, sit amet tempus ex mi id erat.</p>
	<hr />
	<br />

	<h2>Lorem ipsum dolor sit amet!</h2>
	<p>Duis pulvinar semper tortor euismod eleifend. Etiam eget felis ipsum. Mauris quis laoreet libero, vitae lobortis ligula. Maecenas ultrices venenatis mollis. Sed a turpis feugiat, fermentum turpis ac, bibendum nulla. Nullam id justo sit amet ligula congue ullamcorper semper non est. Donec aliquam diam est, eu viverra sapien blandit vehicula. Nam volutpat leo ultricies, consequat sem sed, scelerisque nunc. Aliquam eget aliquam lectus, a aliquam elit.</p>
	<br />
	<h3>Phasellus accumsan eget ex ac porttitor</h3>
	<p>Phasellus iaculis arcu ut ipsum malesuada ultrices. Nunc et augue elit. Pellentesque quis ante malesuada, ultrices dui aliquet, posuere dolor. Curabitur sollicitudin purus vitae odio facilisis finibus. Cras luctus neque id faucibus fringilla. Mauris in pharetra turpis. Morbi ut mattis arcu. Nulla posuere urna eget augue sagittis ornare. Vivamus vehicula massa sit amet ipsum porta tempus. Aenean cursus arcu ut accumsan ultricies. Praesent blandit, erat rhoncus convallis commodo, eros ex consequat tellus, id semper odio leo ac nisi.</p>
	<hr />
	<br />

	<h2>More</h2>
	<p>Sed tempor purus venenatis enim mattis convallis. Maecenas quam orci, porttitor et felis sed, porta tempus est. Praesent condimentum nunc vitae neque suscipit pretium. Fusce feugiat scelerisque arcu, eu vehicula libero tincidunt nec. Curabitur ornare ex mi. Fusce volutpat nisl et arcu laoreet, et placerat nunc posuere. Mauris commodo sit amet velit a maximus. Vivamus turpis enim, fringilla non massa eu, tempus dignissim mauris.</p>
	<hr />
	<br />
</div>";
lmsHtmlEcho( $page );

?>