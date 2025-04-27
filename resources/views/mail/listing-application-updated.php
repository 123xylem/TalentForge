<?php

?>
{{if ($employerAction) }}
<h1>Listing Application Updated</h1>

<p>The listing application for {{ $listing_title }} has been updated.</p>

<p>The applicant {{ $user_name }} has been updated to {{ $application_status }}.</p>

<p>Please review the application and take action as needed.</p>

<p>Thank you,</p>

<p>The Talent Forge Team</p>
{{else}}
  <h1>New Listing Application for - {{ $listing_title }}</h1>

  <p>The applicant {{ $user_name }} has Applied </p>

  {{endif}}