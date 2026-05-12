<?php
session_start();

$ADMIN_PASSWORD = "admin";

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /admin/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: /admin/index.php");
        exit;
    } else {
        $error = "Incorrect password";
    }
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    include 'login.php';
    exit;
}

$dataFile = '../data.json';
$data = json_decode(file_get_contents($dataFile), true);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_data'])) {
    // Basic fields
    $data['hero']['name'] = $_POST['hero_name'];
    $data['hero']['logo_text'] = $_POST['logo_text'];
    $data['hero']['show_image_logo'] = isset($_POST['show_image_logo']);
    $data['hero']['bio'] = trim($_POST['hero_bio'] ?? '');
    if (!empty($_POST['hero_titles'])) {
        $data['hero']['titles'] = array_values(array_filter(array_map('trim', explode("\n", $_POST['hero_titles']))));
    }
    $data['stats']['years_exp'] = $_POST['years_exp'];
    
    $data['about']['email'] = $_POST['about_email'];
    $data['about']['phone'] = $_POST['about_phone'];
    $data['about']['location'] = $_POST['about_location'];
    $data['about']['linkedin'] = $_POST['about_linkedin'];
    $data['about']['github'] = $_POST['about_github'];
    $data['about']['show_github'] = isset($_POST['show_github']);
    $data['about']['languages'] = $_POST['about_languages'];
    
    // Bio Paragraphs (split by lines)
    $bioText = trim($_POST['bio_paragraphs']);
    $data['about']['bio_paragraphs'] = array_values(array_filter(array_map('trim', explode("\n", $bioText))));

    // Achievements & Activities
    $data['achievements'] = array_values(array_filter(array_map('trim', explode("\n", $_POST['achievements']))));
    $data['activities'] = array_values(array_filter(array_map('trim', explode("\n", $_POST['activities']))));

    // Dynamic JSON fields sent from JS
    if (!empty($_POST['experience_json'])) {
        $data['experience'] = json_decode($_POST['experience_json'], true);
    }
    if (!empty($_POST['education_json'])) {
        $data['education'] = json_decode($_POST['education_json'], true);
    }
    if (!empty($_POST['skills_json'])) {
        $data['skills'] = json_decode($_POST['skills_json'], true);
    }
    if (!empty($_POST['projects_json'])) {
        $projects = json_decode($_POST['projects_json'], true);
        foreach ($projects as $idx => $proj) {
            $fileKey = "project_image_" . $idx;
            if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                if (!file_exists('../img/projects')) { mkdir('../img/projects', 0777, true); }
                $ext = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
                $fileName = 'proj_' . time() . '_' . $idx . '.' . $ext;
                $targetFile = '../img/projects/' . $fileName;
                move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile);
                $projects[$idx]['image'] = 'img/projects/' . $fileName;
            }
        }
        $data['projects'] = $projects;
    }

    // Handle Local File Uploads
    if (isset($_FILES['logo_light']) && $_FILES['logo_light']['error'] === UPLOAD_ERR_OK) {
        $targetFile = '../img/logo_light.png';
        move_uploaded_file($_FILES['logo_light']['tmp_name'], $targetFile);
        $data['files']['logo_light'] = 'img/logo_light.png';
    }
    if (isset($_FILES['logo_dark']) && $_FILES['logo_dark']['error'] === UPLOAD_ERR_OK) {
        $targetFile = '../img/logo_dark.png';
        move_uploaded_file($_FILES['logo_dark']['tmp_name'], $targetFile);
        $data['files']['logo_dark'] = 'img/logo_dark.png';
    }
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $targetFile = '../img/profile.png';
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile);
        $data['files']['profile_image'] = 'img/profile.png';
    }
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $targetFile = '../cv/Thilina_Narampanawa_CV.pdf';
        move_uploaded_file($_FILES['cv_file']['tmp_name'], $targetFile);
        $data['files']['cv'] = 'cv/Thilina_Narampanawa_CV.pdf';
    }

    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    $message = "Local data updated successfully! You can now commit and push to Vercel.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Admin CMS | Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; color: #0f172a; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h1 { color: #4f46e5; margin-top: 0; display: flex; justify-content: space-between; align-items: center; }
        .logout-btn { font-size: 1rem; padding: 8px 16px; background: #ef4444; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.3s; }
        .logout-btn:hover { background: #dc2626; }
        
        .section-header { font-size: 1.5rem; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-top: 50px; margin-bottom: 20px; }
        
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #475569; }
        input[type="text"], input[type="number"], input[type="email"], textarea { width: 100%; padding: 12px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 1rem; box-sizing: border-box; transition: 0.3s; font-family: inherit; }
        input:focus, textarea:focus { border-color: #4f46e5; outline: none; }
        
        .btn { padding: 14px 24px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: 0.3s; width: 100%; }
        .btn:hover { background: #4338ca; }
        
        .success { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
        .file-upload { padding: 20px; border: 2px dashed #cbd5e1; border-radius: 8px; margin-bottom: 15px; background: #f8fafc; }
        
        /* Grid Layouts */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        /* Dynamic Cards */
        .dynamic-card { background: #f1f5f9; padding: 25px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #cbd5e1; position: relative; }
        .remove-btn { position: absolute; top: 20px; right: 20px; background: #ef4444; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: bold; transition: 0.3s; }
        .remove-btn:hover { background: #b91c1c; }
        .add-btn { background: #10b981; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 1rem; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
        .add-btn:hover { background: #059669; }
        .hint-text { font-size: 0.85rem; color: #64748b; margin-top: 0; margin-bottom: 8px; }
        
        .save-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: auto;
            padding: 15px 35px;
            font-size: 1.2rem;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
            z-index: 1000;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .save-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.6);
        }
        
        @media (max-width: 768px) {
            .save-btn {
                bottom: 20px;
                right: 20px;
                left: 20px;
                width: auto;
                text-align: center;
                justify-content: center;
                font-size: 1.1rem;
            }
            .grid-2 { grid-template-columns: 1fr; gap: 15px; }
            .container { padding: 25px 20px; border-radius: 12px; }
            h1 { flex-direction: column; align-items: flex-start; gap: 15px; font-size: 1.5rem; }
            .add-btn { width: 100%; justify-content: center; }
            .remove-btn { position: relative; top: 0; right: 0; margin-bottom: 15px; width: 100%; text-align: center; }
            .dynamic-card { padding: 20px 15px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CMS Dashboard <a href="?logout=1" class="logout-btn">Logout</a></h1>
        
        <?php if ($message): ?>
            <div class="success"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="admin-form">
            <input type="hidden" name="update_data" value="1">
            <input type="hidden" name="experience_json" id="hidden_experience_json">
            <input type="hidden" name="education_json" id="hidden_education_json">
            <input type="hidden" name="skills_json" id="hidden_skills_json">
            <input type="hidden" name="projects_json" id="hidden_projects_json">

            <!-- Personal Details -->
            <h2 class="section-header">Personal Details & About</h2>
            <div class="grid-2">
                <div class="form-group">
                    <label>Profile Name</label>
                    <input type="text" name="hero_name" value="<?php echo htmlspecialchars($data['hero']['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Text Logo (Top Left)</label>
                    <input type="text" name="logo_text" value="<?php echo htmlspecialchars($data['hero']['logo_text'] ?? ''); ?>">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Years of Experience Stat</label>
                    <input type="number" name="years_exp" value="<?php echo htmlspecialchars($data['stats']['years_exp']); ?>" required>
                </div>
                <div class="form-group">
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-top:30px;">
                        <input type="checkbox" name="show_image_logo" value="1" <?php echo (!isset($data['hero']['show_image_logo']) || $data['hero']['show_image_logo']) ? 'checked' : ''; ?> style="width:20px; height:20px;">
                        <span style="font-weight:600; font-size:1.1rem;">Enable Image Logo (Overrides Text Logo)</span>
                    </label>
                    <p class="hint-text" style="margin-top:5px;">If unchecked, the website will display your Text Logo instead.</p>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Animated Job Titles (Hero Section)</label>
                    <p class="hint-text">Type each title on a new line. These animate after "A ..."</p>
                    <textarea name="hero_titles" style="height: 100px;" required><?php 
                        echo htmlspecialchars(implode("\n", $data['hero']['titles'] ?? [])); 
                    ?></textarea>
                </div>
                <div class="form-group">
                    <label>Short Bio (Hero Section)</label>
                    <p class="hint-text">This is the text right below your name at the top of the site.</p>
                    <textarea name="hero_bio" style="height: 100px;" required><?php 
                        echo htmlspecialchars($data['hero']['bio'] ?? ''); 
                    ?></textarea>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="about_email" value="<?php echo htmlspecialchars($data['about']['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="about_phone" value="<?php echo htmlspecialchars($data['about']['phone'] ?? ''); ?>" required>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="about_location" value="<?php echo htmlspecialchars($data['about']['location'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Languages (e.g. English, Sinhala)</label>
                    <input type="text" name="about_languages" value="<?php echo htmlspecialchars($data['about']['languages'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>LinkedIn URL</label>
                    <input type="text" name="about_linkedin" value="<?php echo htmlspecialchars($data['about']['linkedin'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>GitHub URL</label>
                    <input type="text" name="about_github" value="<?php echo htmlspecialchars($data['about']['github'] ?? ''); ?>">
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-top:10px;">
                        <input type="checkbox" name="show_github" value="1" <?php echo (!isset($data['about']['show_github']) || $data['about']['show_github']) ? 'checked' : ''; ?> style="width:20px; height:20px;">
                        <span style="font-weight:600; font-size:1rem;">Enable GitHub Link in Footer</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>About Me (Bio Paragraphs)</label>
                <p class="hint-text">Type each paragraph on a new line.</p>
                <textarea name="bio_paragraphs" style="height: 120px;" required><?php 
                    echo htmlspecialchars(implode("\n", $data['about']['bio_paragraphs'] ?? [])); 
                ?></textarea>
            </div>

            <!-- Files -->
            <h2 class="section-header">Files & Assets</h2>
            <div class="grid-2" style="margin-bottom: 20px;">
                <div class="form-group file-upload">
                    <label>Light Theme Logo (.png)</label>
                    <p class="hint-text">Shown when the website is in Light Mode.</p>
                    <div style="margin-bottom: 10px; display: flex; align-items: center; gap: 15px;">
                        <img src="../<?php echo htmlspecialchars($data['files']['logo_light'] ?? ''); ?>" alt="Preview" style="height: 40px; border-radius:4px; border:1px solid #ccc; background:#fff;" onerror="this.style.display='none'">
                    </div>
                    <input type="file" name="logo_light" accept="image/png">
                </div>
                <div class="form-group file-upload">
                    <label>Dark Theme Logo (.png)</label>
                    <p class="hint-text">Shown when the website is in Dark Mode.</p>
                    <div style="margin-bottom: 10px; display: flex; align-items: center; gap: 15px;">
                        <img src="../<?php echo htmlspecialchars($data['files']['logo_dark'] ?? ''); ?>" alt="Preview" style="height: 40px; border-radius:4px; border:1px solid #ccc; background:#1e293b;" onerror="this.style.display='none'">
                    </div>
                    <input type="file" name="logo_dark" accept="image/png">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group file-upload">
                    <label>Update Profile Image (.png or .jpg)</label>
                    <div style="margin-bottom: 10px; display: flex; align-items: center; gap: 15px;">
                        <img src="../<?php echo htmlspecialchars($data['files']['profile_image']); ?>" alt="Profile Preview" style="height: 60px; border-radius:8px; border:1px solid #ccc; background:#fff;" onerror="this.style.display='none'">
                        <p style="margin:5px 0; font-size:0.85rem; color:#64748b;">Current: <?php echo htmlspecialchars($data['files']['profile_image']); ?></p>
                    </div>
                    <input type="file" name="profile_image" accept="image/*">
                </div>
                <div class="form-group file-upload">
                    <label>Update CV File (.pdf)</label>
                    <div style="margin-bottom: 10px;">
                        <a href="../<?php echo htmlspecialchars($data['files']['cv']); ?>" target="_blank" style="display:inline-block; padding:6px 12px; background:#4f46e5; color:white; text-decoration:none; border-radius:6px; font-size:0.9rem; margin-bottom:5px;">View Current CV</a>
                        <p style="margin:5px 0; font-size:0.85rem; color:#64748b;">Current: <?php echo htmlspecialchars($data['files']['cv']); ?></p>
                    </div>
                    <input type="file" name="cv_file" accept="application/pdf">
                </div>
            </div>

            <!-- Experience -->
            <h2 class="section-header">Experience Timeline</h2>
            <button type="button" class="add-btn" onclick="addExperience()">+ Add New Job</button>
            <div id="experience-container"></div>

            <!-- Education -->
            <h2 class="section-header">Education Timeline</h2>
            <button type="button" class="add-btn" onclick="addEducation()">+ Add Education</button>
            <div id="education-container"></div>

            <!-- Skills -->
            <h2 class="section-header">Technical Skills</h2>
            <button type="button" class="add-btn" onclick="addSkill()">+ Add Skill Category</button>
            <div id="skills-container"></div>

                        <!-- Projects -->
            <h2 class="section-header">Undergraduate Projects</h2>
            <button type="button" class="add-btn" onclick="addProject()">+ Add New Project</button>
            <div id="projects-container"></div>

            <!-- Achievements & Activities -->
            <h2 class="section-header">Achievements & Activities</h2>
            <div class="grid-2">
                <div class="form-group">
                    <label>Achievements</label>
                    <p class="hint-text">Type each achievement on a new line.</p>
                    <textarea name="achievements" style="height: 150px;"><?php 
                        echo htmlspecialchars(implode("\n", $data['achievements'] ?? [])); 
                    ?></textarea>
                </div>
                <div class="form-group">
                    <label>Extracurricular Activities</label>
                    <p class="hint-text">Type each activity on a new line.</p>
                    <textarea name="activities" style="height: 150px;"><?php 
                        echo htmlspecialchars(implode("\n", $data['activities'] ?? [])); 
                    ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn save-btn">💾 Save All Changes</button>
        </form>
    </div>

    <script>
        // --- DATA ---
        let experienceData = <?php echo json_encode($data['experience'] ?? []); ?>;
        let educationData = <?php echo json_encode($data['education'] ?? []); ?>;
        let skillsData = <?php echo json_encode($data['skills'] ?? []); ?>;
        let projectsData = <?php echo json_encode($data['projects'] ?? []); ?>;

        // --- EXPERIENCE ---
        const expContainer = document.getElementById('experience-container');
        function renderExperience() {
            expContainer.innerHTML = '';
            experienceData.forEach((item, index) => {
                const bullets = (item.bullets || []).join('\n');
                const card = document.createElement('div');
                card.className = 'dynamic-card exp-card';
                card.innerHTML = `
                    <button type="button" class="remove-btn" onclick="removeExperience(${index})">Remove</button>
                    <div class="grid-2">
                        <div class="form-group"><label>Job Title</label><input type="text" class="exp-title" value="${item.title}" required></div>
                        <div class="form-group"><label>Company</label><input type="text" class="exp-company" value="${item.company}" required></div>
                    </div>
                    <div class="form-group"><label>Date</label><input type="text" class="exp-date" value="${item.date}" required></div>
                    <div class="form-group">
                        <label>Bullets</label>
                        <textarea class="exp-bullets" style="height: 100px;">${bullets}</textarea>
                    </div>
                `;
                expContainer.appendChild(card);
            });
        }
        function updateExperience() {
            experienceData = Array.from(document.querySelectorAll('.exp-card')).map((card, idx) => ({
                id: idx + 1,
                title: card.querySelector('.exp-title').value,
                company: card.querySelector('.exp-company').value,
                date: card.querySelector('.exp-date').value,
                bullets: card.querySelector('.exp-bullets').value.split('\n').filter(b => b.trim() !== '')
            }));
        }
        function addExperience() { updateExperience(); experienceData.unshift({title:"",company:"",date:"",bullets:[]}); renderExperience(); }
        function removeExperience(index) { if(confirm("Remove this job?")) { updateExperience(); experienceData.splice(index, 1); renderExperience(); } }

        // --- EDUCATION ---
        const eduContainer = document.getElementById('education-container');
        function renderEducation() {
            eduContainer.innerHTML = '';
            educationData.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = 'dynamic-card edu-card';
                card.innerHTML = `
                    <button type="button" class="remove-btn" onclick="removeEducation(${index})">Remove</button>
                    <div class="grid-2">
                        <div class="form-group"><label>Degree / Qualification</label><input type="text" class="edu-title" value="${item.title}" required></div>
                        <div class="form-group"><label>Institution</label><input type="text" class="edu-institution" value="${item.institution}" required></div>
                    </div>
                    <div class="form-group"><label>Year</label><input type="text" class="edu-date" value="${item.date}" required></div>
                `;
                eduContainer.appendChild(card);
            });
        }
        function updateEducation() {
            educationData = Array.from(document.querySelectorAll('.edu-card')).map((card, idx) => ({
                id: idx + 1,
                title: card.querySelector('.edu-title').value,
                institution: card.querySelector('.edu-institution').value,
                date: card.querySelector('.edu-date').value
            }));
        }
        function addEducation() { updateEducation(); educationData.unshift({title:"",institution:"",date:""}); renderEducation(); }
        function removeEducation(index) { if(confirm("Remove this education?")) { updateEducation(); educationData.splice(index, 1); renderEducation(); } }

        // --- SKILLS ---
        const skillsContainer = document.getElementById('skills-container');
        function renderSkills() {
            skillsContainer.innerHTML = '';
            skillsData.forEach((cat, index) => {
                // Convert items to easy readable format: "React.js | 90"
                const textItems = (cat.items || []).map(i => `${i.name} | ${i.percent}`).join('\n');
                const card = document.createElement('div');
                card.className = 'dynamic-card skill-card';
                card.innerHTML = `
                    <button type="button" class="remove-btn" onclick="removeSkill(${index})">Remove Category</button>
                    <div class="grid-2">
                        <div class="form-group"><label>Category Name</label><input type="text" class="skill-category" value="${cat.category}" placeholder="e.g. Frontend" required></div>
                        <div class="form-group"><label>FontAwesome Icon</label><input type="text" class="skill-icon" value="${cat.icon}" placeholder="e.g. fa-laptop-code" required></div>
                    </div>
                    <div class="form-group">
                        <label>Skills & Percentages</label>
                        <p class="hint-text">Format: <code>Skill Name | Percentage</code> (e.g. React | 90). Type each on a new line.</p>
                        <textarea class="skill-items" style="height: 100px;">${textItems}</textarea>
                    </div>
                `;
                skillsContainer.appendChild(card);
            });
        }
        function updateSkills() {
            skillsData = Array.from(document.querySelectorAll('.skill-card')).map(card => {
                const lines = card.querySelector('.skill-items').value.split('\n').filter(l => l.trim() !== '');
                const items = lines.map(line => {
                    const parts = line.split('|');
                    return { name: parts[0]?.trim() || "", percent: parseInt(parts[1]) || 50 };
                });
                return {
                    category: card.querySelector('.skill-category').value,
                    icon: card.querySelector('.skill-icon').value,
                    items: items
                };
            });
        }
        function addSkill() { updateSkills();
            updateProjects(); skillsData.push({category:"", icon:"fa-star", items:[]}); renderSkills();
        renderProjects(); }
        function removeSkill(index) { if(confirm("Remove this skill category?")) { updateSkills();
            updateProjects(); skillsData.splice(index, 1); renderSkills();
        renderProjects(); } }

                // --- PROJECTS ---
        const projContainer = document.getElementById('projects-container');
        function renderProjects() {
            projContainer.innerHTML = '';
            projectsData.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = 'dynamic-card proj-card';
                card.innerHTML = `
                    <button type="button" class="remove-btn" onclick="removeProject(${index})">Remove Project</button>
                    <div class="grid-2">
                        <div class="form-group"><label>Project Title</label><input type="text" class="proj-title" value="${item.title}" required></div>
                        <div class="form-group"><label>Year</label><input type="text" class="proj-year" value="${item.year}" required></div>
                    </div>
                    <div class="form-group">
                        <label>Project Image Upload</label>
                        <div style="display:flex; align-items:center; gap:15px; margin-bottom: 10px;">
                            <img src="../${item.image}" alt="Preview" style="height: 60px; border-radius:8px; border:1px solid #cbd5e1; background:#fff;" onerror="this.src='https://via.placeholder.com/100x60';">
                            <input type="text" class="proj-image" value="${item.image}" readonly style="flex:1; background:#e2e8f0; color:#475569;" title="Current Image Path">
                        </div>
                        <input type="file" name="project_image_${index}" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="proj-description" style="height: 80px;">${item.description}</textarea>
                    </div>
                `;
                projContainer.appendChild(card);
            });
        }
        function updateProjects() {
            projectsData = Array.from(document.querySelectorAll('.proj-card')).map((card, idx) => ({
                id: idx + 1,
                title: card.querySelector('.proj-title').value,
                year: card.querySelector('.proj-year').value,
                image: card.querySelector('.proj-image').value,
                description: card.querySelector('.proj-description').value
            }));
        }
        function addProject() { updateProjects(); projectsData.unshift({title:"",year:"",image:"img/projects/placeholder.png",description:""}); renderProjects(); }
        function removeProject(index) { if(confirm("Remove this project?")) { updateProjects(); projectsData.splice(index, 1); renderProjects(); } }

        // --- INIT & SUBMIT ---
        renderExperience();
        renderEducation();
        renderSkills();
        renderProjects();

        document.getElementById('admin-form').addEventListener('submit', function() {
            updateExperience();
            updateEducation();
            updateSkills();
            updateProjects();
            
            document.getElementById('hidden_experience_json').value = JSON.stringify(experienceData);
            document.getElementById('hidden_education_json').value = JSON.stringify(educationData);
            document.getElementById('hidden_skills_json').value = JSON.stringify(skillsData);
            document.getElementById('hidden_projects_json').value = JSON.stringify(projectsData);
        });
    </script>
</body>
</html>
