<?php
/*
 * This class is responsible for the mailing of automated emails for the
 * system. This includes password reset emails, meeting reminders, submission
 * confirmations, etc.
 */

class Mailman {

    //TODO: give this a meaningful value
    private $address = "noreply@example.com";

    /**
     * Sends an email to the user with a link to a password reset page.
     * @param User $user
     */
    public function mailpasswordreset(User $user)
    {
        //TODO: dependency on frontend password reset page?
    }

    /**
     * Creates a meeting notice, and notifies all affiliated users by mail.
     */
    public function mailmeeting()
    {
        //TODO: dependency on iCal/timeanddate functionality
    }

    /**
     * Sends mail to an author confirming their submission of a piece to the system
     * @param string $title
     * @param string $user
     * @param string $email
     */
    public function confirmsubmission(string $title, string $user, string $email)
    {
        $date = getdate();
        $formatteddate = $date["month"] . $date["mday"] . ", " . $date["year"];
        $formattedtime = $date["hours"] . ":" . $date["minutes"];

        $body = "This message confirms your submission of " . $title . " to Echo Cognitio, on "
            . $formatteddate . ", at " . $formattedtime . ".";
        $subject = "Your submission " . $title . " has been received";

        mail($email, $subject, $body);
    }
}
