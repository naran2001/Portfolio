<?php
$data = json_decode(file_get_contents("data.json"), true);
$data["skills"] = [
    [
        "category" => "Frontend & UI/UX",
        "icon" => "fa-laptop-code",
        "items" => [
            ["name" => "React.js & Next.js", "percent" => 90],
            ["name" => "HTML/CSS, Bootstrap & Tailwind", "percent" => 95],
            ["name" => "UI/UX Implementation", "percent" => 85]
        ]
    ],
    [
        "category" => "Backend & APIs",
        "icon" => "fa-server",
        "items" => [
            ["name" => "Laravel & PHP", "percent" => 90],
            ["name" => "Java & Python", "percent" => 80],
            ["name" => "RESTful APIs & Integrations", "percent" => 85]
        ]
    ],
    [
        "category" => "Tools & Methodologies",
        "icon" => "fa-cogs",
        "items" => [
            ["name" => "SQL, MongoDB & PostgreSQL", "percent" => 85],
            ["name" => "Git, GitHub & Version Control", "percent" => 90],
            ["name" => "Agile/Scrum & QA Debugging", "percent" => 85],
            ["name" => "cPanel, Hosting & Moodle LMS", "percent" => 85]
        ]
    ]
];
file_put_contents("data.json", json_encode($data, JSON_PRETTY_PRINT));
echo "Skills undone!";
