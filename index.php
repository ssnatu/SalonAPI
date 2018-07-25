<?php
/**
 * 
 */
require_once 'api/api.inc.php';

if (isset($_GET['find_nearest_postcode']) && !empty($_GET['find_nearest_postcode']))
{
	$user_postcode = strip_tags(trim($_GET['find_nearest_postcode']));
	$shop_details = get_address_and_opening_times($user_postcode);
}

?>

<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="utf-8" />
    <title>API example to find nearest location and address of Beauty Salon using its postcode</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style1.css" type="text/css" />
</head>
<body>
<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">
	        <h2>Beauty Salon API - Find nearest Beauty Salon</h2>
	    </div>
	    <div class="panel-body">
		    <form id="beautySalonUI" name="beautySalonUI">
            <div class="row">
            <div class="col-xs-12 col-sm-12 col-lg-12">
		    	<label>Type Postcode to find nearest Beauty Salon location: </label>
		    	<input type="text" name="find_nearest_postcode" id="find_nearest_postcode" placeholder="BD3 7AT" required="required" value="<?php 
                    if (isset($_GET['find_nearest_postcode'])):
                        echo trim(htmlspecialchars($_GET['find_nearest_postcode']));
                    endif; ?>">
            </div></div>

            <div class="row">
            <div class="col-xs-6 col-sm-6 col-lg-6">
		    	<input type="submit" name="submit">
            </div></div>
		    </form>
	    </div>
    </div>
    <?php
	if (!empty($shop_details))
	{
	?>
    <div class="panel panel-info">
    	<div class="panel-body">
    		<?php
    			echo "<b>Your nearest Beauty Salon location is: </b>" . "<br>";
                $out = '';
                if (!empty($shop_details['address']['street_number']) &&
                    !empty($shop_details['address']['route']))
                {
                    $out .= $shop_details['address']['street_number'] . ', ' . $shop_details['address']['route'];
                    $out .= "<br>";
                }
    			
    			if (!empty($shop_details['address']['postal_town']) &&
                    !empty($shop_details['address']['administrative_area_level_2']))
                {
                    $out .= $shop_details['address']['postal_town'] . ', ' . $shop_details['address']['administrative_area_level_2'];
                    $out .= "<br>";
                }
    			
    			$out .= $shop_details['address']['postal_code'] . "<br>";

                if (!empty($shop_details['address']['administrative_area_level_1']) &&
                    !empty($shop_details['address']['country']))
                {
                    $out .= $shop_details['address']['administrative_area_level_1'] . ', ' . $shop_details['address']['country'];
                }
    			$out .= "<br><br>";
                echo $out;
    			
    			echo "<b>Opening times: </b>" . "<br>";
    			foreach ($shop_details['opening_times'] as $day => $time)
    			{
    				if ($time != '-')
    				{
    					echo $day . ': ' . $time;
    					echo "<br>";
    				}
    				else
    				{
    					echo $day . ': Closed';
    					echo "<br>"; 
    				}
    			}
    			
    		?>
    	</div>
    </div>
    <?php } ?>
</div>
</body>
</html>
