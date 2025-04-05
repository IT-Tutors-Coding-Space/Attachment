<?php
function getPasswordResetEmail($name, $reset_link) {
    $project_name = "AttachME";
    
    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #007bff; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; }
            .button { 
                display: inline-block; padding: 10px 20px; 
                background-color: #007bff; color: white; 
                text-decoration: none; border-radius: 5px; 
            }
            .footer { margin-top: 20px; font-size: 0.9em; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>$project_name Password Reset</h2>
            </div>
            <div class='content'>
                <p>Hello,</p>
                <p>We received a request to reset your $project_name account password. Click the button below to reset it:</p>
                <p><a href='$reset_link' class='button'>Reset Password</a></p>
                <p>If you didn't request this, you can safely ignore this email.</p>
                <p>This link will expire in 30 minutes.</p>
            </div>
            <div class='footer'>
                <p>Best regards,<br>The $project_name Team</p>
            </div>
        </div>
    </body>
    </html>
    ";
}
?>
