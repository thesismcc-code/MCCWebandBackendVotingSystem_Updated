# Election Control - User Guide

## What You'll See

### 1. Current Election Settings Display (NEW!)
When you have an active election, you'll see a display at the top showing:

```
┌─────────────────────────────────────────────────────────────┐
│ Current Election Settings              [Edit General Settings]│
├─────────────────────────────────────────────────────────────┤
│ Election Name          Semester           Academic Year      │
│ College of Engineering  2nd Semester      2025-2026         │
│ Elections 2025                                               │
├─────────────────────────────────────────────────────────────┤
│ Schedule Information                      [Edit Schedule]    │
├─────────────────────────────────────────────────────────────┤
│ Date From    Date To      Opening Time   Closing Time       │
│ Apr 15, 2026 Apr 17, 2026 08:00 AM       05:00 PM          │
└─────────────────────────────────────────────────────────────┘
```

### 2. General Settings Modal

**Semester Field:**
```
Semester: [Select Semester ▼]
          - 1st Semester
          - 2nd Semester
```
✅ Only 2 options (Summer removed)

**Academic Year Field:**
```
Academic Year: [2025-2026]  ← Type directly, no dropdown!
```
✅ Text input - just type the year
✅ Format: YYYY-YYYY (e.g., 2025-2026)

### 3. How to Use

#### Creating First Election:
1. Click "General Settings" card
2. Enter:
   - Election Name: "College of Engineering Elections 2025"
   - Semester: Select "2nd Semester"
   - Academic Year: Type "2025-2026"
3. Click "Save"
4. ✅ Saved to Firebase!
5. Current settings display appears

#### Editing Election:
1. Look at "Current Election Settings" at top
2. Click "Edit General Settings" button
3. Modal opens with current data filled in
4. Change any field (e.g., change semester to "1st Semester")
5. Click "Save"
6. ✅ Updated in Firebase!
7. Display refreshes with new data

#### Adding Schedule:
1. Click "Schedule Settings" card OR "Edit Schedule" button
2. Fill in dates and times
3. Click "Save"
4. ✅ Schedule saved to Firebase!
5. Schedule section appears in display

## What Happens in Firebase

### Before Saving:
```
elections/
  (empty)
```

### After Saving General Settings:
```
elections/
  -NxYz123abc/
    election_name: "College of Engineering Elections 2025"
    semester: "2nd Semester"
    academic_year: "2025-2026"
    status: "active"
    created_at: "2026-04-15T10:30:00+00:00"
    updated_at: "2026-04-15T10:30:00+00:00"
```

### After Adding Schedule:
```
elections/
  -NxYz123abc/
    election_name: "College of Engineering Elections 2025"
    semester: "2nd Semester"
    academic_year: "2025-2026"
    date_from: "2026-04-15"
    date_to: "2026-04-17"
    opening_time: "08:00"
    closing_time: "17:00"
    status: "active"
    created_at: "2026-04-15T10:30:00+00:00"
    updated_at: "2026-04-15T10:30:00+00:00"
```

## Success Messages

When you save:
```
┌─────────────────────────────────────────────┐
│ ✓ General settings saved successfully.      │
└─────────────────────────────────────────────┘
```
(Auto-dismisses after 5 seconds)

## Error Messages

If you enter wrong format:
```
┌─────────────────────────────────────────────┐
│ ✗ Academic Year must be in format YYYY-YYYY │
│   (e.g., 2025-2026)                         │
└─────────────────────────────────────────────┘
```

## Key Features

✅ **Real-time**: Changes save immediately to Firebase
✅ **Edit anytime**: Click edit buttons to modify settings
✅ **Pre-filled forms**: Current data loads automatically
✅ **Validation**: Prevents invalid data
✅ **User-friendly**: Clear messages and formatting
✅ **No scrolling**: Academic Year is text input, not dropdown
✅ **Only 2 semesters**: 1st and 2nd (Summer removed)

## Tips

1. **Academic Year Format**: Always use YYYY-YYYY (e.g., 2025-2026)
2. **Editing**: Use the edit buttons in the display section
3. **Schedule**: Add schedule after creating general settings
4. **Validation**: Red messages tell you what to fix
5. **Success**: Green messages confirm your save worked
