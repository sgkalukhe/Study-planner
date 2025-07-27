<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: login.php");
    exit();
}

try {
    // Fetch user study preferences
    $stmt = $pdo->prepare("SELECT study_start_date, study_end_date, daily_start_time, daily_end_time FROM user_study_settings WHERE user_id = ?");
    $stmt->execute([$userId]);
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$settings) {
        throw new Exception("Study preferences not set.");
    }

    $startDate = new DateTime($settings['study_start_date']);
    $endDate = new DateTime($settings['study_end_date']);
    $startTimeRaw = $settings['daily_start_time'];
    $endTimeRaw = $settings['daily_end_time'];

    $dailyStart = new DateTime($startTimeRaw);
    $dailyEnd = new DateTime($endTimeRaw);

    if ($dailyStart >= $dailyEnd) {
        throw new Exception("Start time must be earlier than end time.");
    }

    $dailyHours = (int)$dailyStart->diff($dailyEnd)->h;
    if ($dailyHours < 1) {
        throw new Exception("At least 1 hour of daily study time required.");
    }

    // Fetch subject difficulties
    $stmt = $pdo->prepare("
        SELECT s.name AS subject_name, usd.difficulty 
        FROM user_subject_difficulties usd
        JOIN subjects s ON usd.subject_id = s.id
        WHERE usd.user_id = ?
    ");
    $stmt->execute([$userId]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($subjects)) {
        throw new Exception("No subjects found for this user.");
    }

    // Remove old recommended plans
    $pdo->prepare("DELETE FROM study_plans WHERE user_id = ? AND status = 'recommended'")->execute([$userId]);

    // Sort subjects by difficulty
    $difficultyMap = ['1' => [], '2' => [], '3' => []]; // 1=Easy, 2=Medium, 3=Hard
    foreach ($subjects as $subj) {
        $difficultyMap[$subj['difficulty']][] = $subj['subject_name'];
    }

    // Define weight percentages
    $weights = [
        '3' => 0.5, // Hard
        '2' => 0.3, // Medium
        '1' => 0.2  // Easy
    ];

    // Prepare insert statement
    $insertStmt = $pdo->prepare("
        INSERT INTO study_plans (user_id, title, subject, date, start_time, end_time, status)
        VALUES (?, ?, ?, ?, ?, ?, 'recommended')
    ");

    $currentDate = clone $startDate;
    while ($currentDate <= $endDate) {
        $dailySlots = [];

        foreach (['3', '2', '1'] as $level) {
            $subjectsOfLevel = $difficultyMap[$level];
            if (empty($subjectsOfLevel)) continue;

            $levelHours = floor($weights[$level] * $dailyHours);
            $remainingSubjects = count($subjectsOfLevel);
            $hoursPerSubject = intdiv($levelHours, $remainingSubjects);
            $extra = $levelHours % $remainingSubjects;

            foreach ($subjectsOfLevel as $i => $subject) {
                $hours = $hoursPerSubject + ($i < $extra ? 1 : 0);
                for ($j = 0; $j < $hours; $j++) {
                    $dailySlots[] = $subject;
                }
            }
        }

        // Fill remaining hours if needed (due to rounding)
        while (count($dailySlots) < $dailyHours) {
            $allSubjects = array_merge($difficultyMap['3'], $difficultyMap['2'], $difficultyMap['1']);
            if (!empty($allSubjects)) {
                $dailySlots[] = $allSubjects[array_rand($allSubjects)];
            } else {
                break;
            }
        }

        shuffle($dailySlots);

        // Insert sessions
        $slotStart = clone $dailyStart;
        foreach ($dailySlots as $subject) {
            $slotEnd = clone $slotStart;
            $slotEnd->modify('+1 hour');

            $insertStmt->execute([
                $userId,
                "Recommended: $subject",
                $subject,
                $currentDate->format("Y-m-d"),
                $slotStart->format("H:i"),
                $slotEnd->format("H:i")
            ]);

            $slotStart = $slotEnd;
        }

        $currentDate->modify('+1 day');
    }

    header("Location: view-plan.php?msg=generated");
    exit();

} catch (Exception $e) {
    echo "Error generating recommendations: " . $e->getMessage();
}
