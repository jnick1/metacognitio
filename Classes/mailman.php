<?php
/*
 * This class is responsible for the mailing of automated emails for the
 * system. This includes password reset emails, meeting reminders, submission
 * confirmations, etc.
 */

class Mailman {

    //TODO: give this a meaningful value
    private $address = "noreply@example.com";

    public function mailpasswordreset(string $user, string $email) {
        //TODO: dependency on frontend password reset page?
}
    public function mailmeeting() {
        //TODO: dependency on iCal/timeanddate functionality
    }
    public function confirmsubmission(string $title, string $user, string $email) {
        $date = getdate();
        $formatteddate = $date["month"] . $date["mday"] . ", " . $date["year"];
        $formattedtime = $date["hours"] . ":" . $date["minutes"];

        $body = "This message confirms your submission of " . $title . " to Echo Cognitio, on "
            . $formatteddate . ", at " . $formattedtime . ".";
        $subject = "Your submission " . $title . " has been received";

        mail($email, $subject, $body);
    }
}