<?php
/*
Template Name: Get A Quote
*/

get_header();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lugo_quote_nonce']) && wp_verify_nonce($_POST['lugo_quote_nonce'], 'lugo_quote')) {
    $fields = array(
        'full_name' => sanitize_text_field($_POST['full_name'] ?? ''),
        'company_name' => sanitize_text_field($_POST['company_name'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        'pickup_address' => sanitize_text_field($_POST['pickup_address'] ?? ''),
        'pickup_contact' => sanitize_text_field($_POST['pickup_contact'] ?? ''),
        'on_site_equipment' => sanitize_text_field($_POST['on_site_equipment'] ?? ''),
        'delivery_address' => sanitize_text_field($_POST['delivery_address'] ?? ''),
        'delivery_contact' => sanitize_text_field($_POST['delivery_contact'] ?? ''),
        'delivery_hours' => sanitize_textarea_field($_POST['delivery_hours'] ?? ''),
        'equipment_type' => sanitize_text_field($_POST['equipment_type'] ?? ''),
        'other_equipment' => sanitize_text_field($_POST['other_equipment'] ?? ''),
        'make_model' => sanitize_text_field($_POST['make_model'] ?? ''),
        'weight' => sanitize_text_field($_POST['weight'] ?? ''),
        'dimensions' => sanitize_text_field($_POST['dimensions'] ?? ''),
        'pickup_date' => sanitize_text_field($_POST['pickup_date'] ?? ''),
        'date_flexible' => sanitize_text_field($_POST['date_flexible'] ?? ''),
        'is_emergency' => sanitize_text_field($_POST['is_emergency'] ?? ''),
        'extra_notes' => sanitize_textarea_field($_POST['extra_notes'] ?? ''),
    );

    // Prepare email
    $subject = 'New Quote Request from ' . $fields['full_name'];
    $message = "Quote details:\n";
    foreach ($fields as $label => $value) {
        $message .= $label . ': ' . $value . "\n";
    }
    if (!empty($_FILES['upload']['name'][0])) {
        $attachments = array();
        foreach ($_FILES['upload']['name'] as $key => $name) {
            if ($_FILES['upload']['size'][$key] <= 10485760 && !$_FILES['upload']['error'][$key]) {
                $tmp = $_FILES['upload']['tmp_name'][$key];
                $uploaded = wp_upload_bits($name, null, file_get_contents($tmp));
                if (empty($uploaded['error'])) {
                    $attachments[] = $uploaded['file'];
                }
            }
        }
    }
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    wp_mail('quotes@lugospecialized.com', $subject, $message, $headers, $attachments ?? array());

    // Save submission as custom post type
    $post_id = wp_insert_post(array(
        'post_type' => 'quote_request',
        'post_title' => $subject,
        'post_status' => 'publish',
        'meta_input' => $fields,
    ));

    echo '<div class="quote-confirm">Thanks, your quote request is being reviewed! We’ll get back to you shortly.</div>';
}
?>

<style>
  body {
    font-family: 'Montserrat', sans-serif;
    background-color: #fff;
  }
  .lugo-form {
    max-width: 800px;
    margin: auto;
    padding: 20px;
    background: #fafafa;
    border: 1px solid #ddd;
  }
  .lugo-form h2 {
    color: #800000; /* Maroon */
    text-align: center;
  }
  .lugo-form label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
  }
  .lugo-form input,
  .lugo-form select,
  .lugo-form textarea {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    box-sizing: border-box;
  }
  .lugo-form button {
    background-color: #ffcc00; /* Caterpillar Yellow */
    color: #000;
    padding: 10px 20px;
    border: none;
    font-size: 18px;
    cursor: pointer;
    margin-top: 20px;
  }
  .quote-confirm {
    padding: 10px;
    background: #dff0d8;
    color: #3c763d;
    margin: 15px 0;
    text-align: center;
  }
  .sticky-btn {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #000;
    text-align: center;
    padding: 10px;
  }
  .sticky-btn button {
    background-color: #ffcc00;
    color: #000;
    font-size: 20px;
    padding: 15px 30px;
  }
</style>

<div class="lugo-form">
  <h2>Need It Hauled? Get Your Fast Quote Now 🚚</h2>
  <p>We specialize in equipment and machinery transport – get a custom quote from Lugo Specialized Transportation within 24 hours.</p>
  <form method="post" enctype="multipart/form-data" id="lugo-quote-form">
    <?php wp_nonce_field('lugo_quote', 'lugo_quote_nonce'); ?>
    <label>Full Name *</label>
    <input type="text" name="full_name" required autocomplete="name" />

    <label>Company Name</label>
    <input type="text" name="company_name" autocomplete="organization" />

    <label>Email *</label>
    <input type="email" name="email" required autocomplete="email" />

    <label>Phone Number *</label>
    <input type="tel" name="phone" required autocomplete="tel" />

    <label>Pickup Address *</label>
    <input type="text" name="pickup_address" id="pickup_address" required />

    <label>Pickup Contact Name & Phone</label>
    <input type="text" name="pickup_contact" />

    <label>On-Site Equipment Type</label>
    <select name="on_site_equipment">
      <option value="Forklift on site">Forklift on site</option>
      <option value="Loading dock">Loading dock</option>
      <option value="Crane">Crane</option>
      <option value="Manual loading required">Manual loading required</option>
      <option value="Unknown">Unknown</option>
    </select>

    <label>Delivery Address *</label>
    <input type="text" name="delivery_address" id="delivery_address" required />

    <label>Delivery Contact Name & Phone</label>
    <input type="text" name="delivery_contact" />

    <label>Delivery Hours or Restrictions</label>
    <textarea name="delivery_hours"></textarea>

    <label>Equipment Type *</label>
    <select name="equipment_type" id="equipment_type" required>
      <option value="Skid Steer">Skid Steer</option>
      <option value="Mini Excavator">Mini Excavator</option>
      <option value="Forklift">Forklift</option>
      <option value="Boom Lift">Boom Lift</option>
      <option value="Generator">Generator</option>
      <option value="Other">Other (specify below)</option>
    </select>

    <div id="other-equipment-field" style="display:none;">
      <label>Other Equipment</label>
      <input type="text" name="other_equipment" />
    </div>

    <label>Make/Model</label>
    <input type="text" name="make_model" />

    <label>Estimated Weight (lbs) *</label>
    <input type="number" name="weight" required />

    <label>Dimensions (L x W x H in feet)</label>
    <input type="text" name="dimensions" />

    <label>Preferred Pickup Date *</label>
    <input type="date" name="pickup_date" required />
    <label>
      <input type="checkbox" name="date_flexible" value="Yes" /> Is this flexible?
    </label>

    <label>
      <input type="checkbox" name="is_emergency" value="Yes" /> This is a last-minute or emergency haul 🚨
    </label>

    <label>Additional Notes or Instructions</label>
    <textarea name="extra_notes"></textarea>

    <label>Upload Images or Documents (max 10MB each)</label>
    <input type="file" name="upload[]" multiple />

    <button type="submit">Request My Quote 💥</button>
  </form>
</div>

<div class="sticky-btn">
  <button form="lugo-quote-form">Request My Quote 💥</button>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_API_KEY&libraries=places"></script>
<script>
  function initAutocomplete() {
    const options = { componentRestrictions: { country: 'us' } };
    new google.maps.places.Autocomplete(document.getElementById('pickup_address'), options);
    new google.maps.places.Autocomplete(document.getElementById('delivery_address'), options);
  }
  google.maps.event.addDomListener(window, 'load', initAutocomplete);

  document.getElementById('equipment_type').addEventListener('change', function() {
    document.getElementById('other-equipment-field').style.display = this.value === 'Other' ? 'block' : 'none';
  });
</script>
<?php
get_footer();
?>
