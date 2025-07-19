CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    role ENUM('admin', 'student') NOT NULL
);
CREATE TABLE complaints (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11) DEFAULT NULL,
    complaint TEXT NOT NULL,
    status ENUM('Pending', 'Resolved') DEFAULT 'Pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX (student_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE fees (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11) DEFAULT NULL,
    amount_due DECIMAL(10,2) DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    status ENUM('Paid', 'Unpaid') DEFAULT 'Unpaid',
    INDEX (student_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE SET NULL
);
CREATE TABLE rooms (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) DEFAULT NULL,
    capacity INT(11) DEFAULT NULL,
    occupants INT(11) DEFAULT 0,
    INDEX (room_number)
);
CREATE TABLE room_allocation (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11) DEFAULT NULL,
    room_id INT(11) DEFAULT NULL,
    INDEX (student_id),
    INDEX (room_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);
CREATE TABLE logs (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    action TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TRIGGER after_room_assign
AFTER INSERT ON room_allocation
FOR EACH ROW
BEGIN
    -- Example: Insert a log entry when a room is allocated to a student
    INSERT INTO activity_log (action)
    VALUES (CONCAT('Room ', NEW.room_id, ' assigned to student ', NEW.student_id));
END;
CREATE TRIGGER after_student_delete
AFTER DELETE ON users
FOR EACH ROW
BEGIN
    -- Delete complaints related to the student
    DELETE FROM complaints WHERE student_id = OLD.id;

    -- Delete fees related to the student
    DELETE FROM fees WHERE student_id = OLD.id;

    -- Optionally, you can log this deletion in the activity log
    INSERT INTO activity_log (action)
    VALUES (CONCAT('Student with ID ', OLD.id, ' deleted from the system.'));
END;
CREATE TRIGGER log_room_deletion
AFTER DELETE ON rooms
FOR EACH ROW
BEGIN
    -- Log the room deletion in the activity log
    INSERT INTO activity_log (action)
    VALUES (CONCAT('Room ', OLD.room_number, ' deleted.'));
END;
