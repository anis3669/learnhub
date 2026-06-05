<?php

namespace Database\Seeders;

use App\Models\SkillAssessmentQuestion;
use Illuminate\Database\Seeder;

class SkillAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        if (SkillAssessmentQuestion::count() > 0) return;

        $questions = [
            // Basic (difficulty = 'basic')
            ['question_text' => 'What does HTML stand for?', 'option_a' => 'Hyper Text Markup Language', 'option_b' => 'High Text Machine Language', 'option_c' => 'Hyper Transfer Markup Language', 'option_d' => 'None of these', 'correct_option' => 'a', 'difficulty' => 'basic', 'topic' => 'Web Basics'],
            ['question_text' => 'Which symbol starts a single-line comment in Python?', 'option_a' => '//', 'option_b' => '#', 'option_c' => '/*', 'option_d' => '--', 'correct_option' => 'b', 'difficulty' => 'basic', 'topic' => 'Python'],
            ['question_text' => 'What is the output of print(2 + 3) in Python?', 'option_a' => '"2 + 3"', 'option_b' => '23', 'option_c' => '5', 'option_d' => 'Error', 'correct_option' => 'c', 'difficulty' => 'basic', 'topic' => 'Python'],
            ['question_text' => 'Which HTML tag creates a hyperlink?', 'option_a' => '<link>', 'option_b' => '<href>', 'option_c' => '<a>', 'option_d' => '<nav>', 'correct_option' => 'c', 'difficulty' => 'basic', 'topic' => 'Web Basics'],
            ['question_text' => 'What does CSS stand for?', 'option_a' => 'Cascading Style Sheets', 'option_b' => 'Computer Style Syntax', 'option_c' => 'Creative Styling System', 'option_d' => 'Cascading Syntax Style', 'correct_option' => 'a', 'difficulty' => 'basic', 'topic' => 'Web Basics'],
            ['question_text' => 'Which Python function converts a string to an integer?', 'option_a' => 'str()', 'option_b' => 'float()', 'option_c' => 'int()', 'option_d' => 'convert()', 'correct_option' => 'c', 'difficulty' => 'basic', 'topic' => 'Python'],
            ['question_text' => 'In Python, how do you create a list?', 'option_a' => 'list = {}', 'option_b' => 'list = []', 'option_c' => 'list = ()', 'option_d' => 'list = <>', 'correct_option' => 'b', 'difficulty' => 'basic', 'topic' => 'Python'],
            ['question_text' => 'Which keyword is used to define a function in Python?', 'option_a' => 'function', 'option_b' => 'define', 'option_c' => 'func', 'option_d' => 'def', 'correct_option' => 'd', 'difficulty' => 'basic', 'topic' => 'Python'],
            ['question_text' => 'What is the correct PHP syntax to end a statement?', 'option_a' => ';', 'option_b' => '.', 'option_c' => ':', 'option_d' => '>>', 'correct_option' => 'a', 'difficulty' => 'basic', 'topic' => 'PHP'],
            ['question_text' => 'Which of the following is NOT a programming language?', 'option_a' => 'Python', 'option_b' => 'HTML', 'option_c' => 'Java', 'option_d' => 'C++', 'correct_option' => 'b', 'difficulty' => 'basic', 'topic' => 'General'],

            // Intermediate (difficulty = 'intermediate')
            ['question_text' => 'What is the time complexity of binary search?', 'option_a' => 'O(n)', 'option_b' => 'O(n²)', 'option_c' => 'O(log n)', 'option_d' => 'O(1)', 'correct_option' => 'c', 'difficulty' => 'intermediate', 'topic' => 'Algorithms'],
            ['question_text' => 'Which design pattern separates an application into Model, View, and Controller?', 'option_a' => 'Singleton', 'option_b' => 'Observer', 'option_c' => 'Factory', 'option_d' => 'MVC', 'correct_option' => 'd', 'difficulty' => 'intermediate', 'topic' => 'Design Patterns'],
            ['question_text' => 'What does OOP stand for?', 'option_a' => 'Object-Oriented Programming', 'option_b' => 'Open Output Processing', 'option_c' => 'Ordered Object Parsing', 'option_d' => 'Optional Output Protocol', 'correct_option' => 'a', 'difficulty' => 'intermediate', 'topic' => 'OOP'],
            ['question_text' => 'In SQL, which clause filters rows after GROUP BY?', 'option_a' => 'WHERE', 'option_b' => 'HAVING', 'option_c' => 'FILTER', 'option_d' => 'LIMIT', 'correct_option' => 'b', 'difficulty' => 'intermediate', 'topic' => 'SQL'],
            ['question_text' => 'What is a foreign key in a relational database?', 'option_a' => 'A key used for encryption', 'option_b' => 'A primary key of another table referenced here', 'option_c' => 'A column that must be unique', 'option_d' => 'An auto-increment column', 'correct_option' => 'b', 'difficulty' => 'intermediate', 'topic' => 'Database'],
            ['question_text' => 'Which HTTP method is used to update a resource?', 'option_a' => 'GET', 'option_b' => 'POST', 'option_c' => 'DELETE', 'option_d' => 'PUT', 'correct_option' => 'd', 'difficulty' => 'intermediate', 'topic' => 'HTTP'],
            ['question_text' => 'What is a closure in JavaScript?', 'option_a' => 'A method to close the browser', 'option_b' => 'A function that retains access to its outer scope', 'option_c' => 'A way to end a loop', 'option_d' => 'A CSS property', 'correct_option' => 'b', 'difficulty' => 'intermediate', 'topic' => 'JavaScript'],
            ['question_text' => 'What does SOLID stand for in software engineering?', 'option_a' => 'A set of 5 object-oriented design principles', 'option_b' => 'A database schema design guide', 'option_c' => 'A type of data structure', 'option_d' => 'A testing framework', 'correct_option' => 'a', 'difficulty' => 'intermediate', 'topic' => 'Design'],
            ['question_text' => 'In Python, what is a decorator?', 'option_a' => 'A class that inherits from another', 'option_b' => 'A CSS-like styling module', 'option_c' => 'A function that wraps another function', 'option_d' => 'A loop construct', 'correct_option' => 'c', 'difficulty' => 'intermediate', 'topic' => 'Python'],
            ['question_text' => 'What is the purpose of an index in a database?', 'option_a' => 'To encrypt data', 'option_b' => 'To speed up query lookups', 'option_c' => 'To create backups', 'option_d' => 'To limit row count', 'correct_option' => 'b', 'difficulty' => 'intermediate', 'topic' => 'Database'],

            // Advanced (difficulty = 'advanced')
            ['question_text' => 'What is the CAP theorem in distributed systems?', 'option_a' => 'Consistency, Availability, Partition Tolerance trade-off', 'option_b' => 'A caching protocol', 'option_c' => 'CPU, Algorithm, Process theorem', 'option_d' => 'A SQL query optimizer', 'correct_option' => 'a', 'difficulty' => 'advanced', 'topic' => 'Distributed Systems'],
            ['question_text' => 'Which algorithm solves the shortest path problem in a weighted graph?', 'option_a' => 'BFS', 'option_b' => 'DFS', 'option_c' => "Dijkstra's", 'option_d' => 'Kruskal\'s', 'correct_option' => 'c', 'difficulty' => 'advanced', 'topic' => 'Graph Algorithms'],
            ['question_text' => 'What is memoization?', 'option_a' => 'A way to write readable code', 'option_b' => 'Caching function results to avoid recomputation', 'option_c' => 'A type of recursion', 'option_d' => 'A memory allocation technique', 'correct_option' => 'b', 'difficulty' => 'advanced', 'topic' => 'Algorithms'],
            ['question_text' => 'What is the time complexity of Merge Sort in the worst case?', 'option_a' => 'O(n²)', 'option_b' => 'O(n)', 'option_c' => 'O(log n)', 'option_d' => 'O(n log n)', 'correct_option' => 'd', 'difficulty' => 'advanced', 'topic' => 'Algorithms'],
            ['question_text' => 'What does ACID stand for in database transactions?', 'option_a' => 'Atomicity, Consistency, Isolation, Durability', 'option_b' => 'Access, Control, Integrity, Data', 'option_c' => 'Aggregation, Caching, Indexing, Durability', 'option_d' => 'Authentication, Consistency, Integrity, Design', 'correct_option' => 'a', 'difficulty' => 'advanced', 'topic' => 'Database'],
            ['question_text' => 'In machine learning, what is overfitting?', 'option_a' => 'Training too fast', 'option_b' => 'A model that performs well on training data but poorly on new data', 'option_c' => 'Using too little training data', 'option_d' => 'A gradient descent error', 'correct_option' => 'b', 'difficulty' => 'advanced', 'topic' => 'Machine Learning'],
            ['question_text' => 'What is the purpose of the Docker container technology?', 'option_a' => 'Database optimization', 'option_b' => 'UI testing', 'option_c' => 'Packaging and isolating applications with dependencies', 'option_d' => 'Code versioning', 'correct_option' => 'c', 'difficulty' => 'advanced', 'topic' => 'DevOps'],
            ['question_text' => 'What is a race condition in concurrent programming?', 'option_a' => 'A performance benchmark test', 'option_b' => 'When two threads compete to access shared data, causing bugs', 'option_c' => 'A CPU scheduling algorithm', 'option_d' => 'A memory leak pattern', 'correct_option' => 'b', 'difficulty' => 'advanced', 'topic' => 'Concurrency'],
            ['question_text' => 'What is the purpose of a JWT (JSON Web Token)?', 'option_a' => 'Compress JSON data', 'option_b' => 'Transfer data between JavaScript and PHP', 'option_c' => 'Securely transmit information between parties as a compact token', 'option_d' => 'Generate random user IDs', 'correct_option' => 'c', 'difficulty' => 'advanced', 'topic' => 'Security'],
            ['question_text' => 'What is eventual consistency in distributed systems?', 'option_a' => 'Data is always consistent across all nodes instantly', 'option_b' => 'All nodes will converge to the same data given no new updates', 'option_c' => 'Consistency is never guaranteed', 'option_d' => 'A type of database index', 'correct_option' => 'b', 'difficulty' => 'advanced', 'topic' => 'Distributed Systems'],
        ];

        foreach ($questions as $q) {
            SkillAssessmentQuestion::create($q);
        }

        $this->command->info('✅ SkillAssessmentSeeder: ' . count($questions) . ' questions seeded.');
    }
}
