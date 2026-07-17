<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $statusType; // 'DOWN', 'RECOVERY', atau 'WARNING'
    public $issueMessage;

    public function __construct(Project $project, $statusType, $issueMessage)
    {
        $this->project = $project;
        $this->statusType = $statusType;
        $this->issueMessage = $issueMessage;
    }

    public function build()
    {
        // Tentukan emoji subject berdasarkan status
        $emoji = $this->statusType === 'DOWN' ? '🚨' : ($this->statusType === 'RECOVERY' ? '✅' : '⚠️');
        $subject = "{$emoji} [{$this->statusType}] Alert: {$this->project->name}";

        return $this->subject($subject)
                    ->view('emails.project_status');
    }
}