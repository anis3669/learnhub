<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\QuestionBank;
use App\Models\CoursePrerequisite;
use Illuminate\Database\Seeder;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $course1 = Course::where('title', 'like', '%Python%')->first();
        $course2 = Course::where('title', 'like', '%Laravel%')->first();
        $course3 = Course::where('title', 'like', '%Data Structures%')->first();
        $course4 = Course::where('title', 'like', '%Machine Learning%')->first();

        // ── Course 1: Python Programming ─────────────────────────────────────
        if ($course1 && QuestionBank::where('course_id', $course1->id)->count() < 30) {
            $pythonQs = [
                ['q' => 'What is the output of print(type(3.14))?', 'a' => "<class 'int'>", 'b' => "<class 'float'>", 'c' => "<class 'double'>", 'd' => "<class 'number'>", 'correct' => 2],
                ['q' => 'Which method removes the last element of a Python list?', 'a' => 'remove()', 'b' => 'delete()', 'c' => 'pop()', 'd' => 'discard()', 'correct' => 3],
                ['q' => 'What is the result of 10 % 3?', 'a' => '3', 'b' => '1', 'c' => '0', 'd' => '2', 'correct' => 2],
                ['q' => 'Which keyword exits a loop in Python?', 'a' => 'exit', 'b' => 'stop', 'c' => 'break', 'd' => 'end', 'correct' => 3],
                ['q' => 'How do you create a dictionary in Python?', 'a' => '[]', 'b' => '()', 'c' => '<>', 'd' => '{}', 'correct' => 4],
                ['q' => 'What does len([1,2,3]) return?', 'a' => '1', 'b' => '2', 'c' => '3', 'd' => '4', 'correct' => 3],
                ['q' => 'Which of these is a tuple?', 'a' => '[1,2,3]', 'b' => '{1,2,3}', 'c' => '(1,2,3)', 'd' => 'tuple{1,2,3}', 'correct' => 3],
                ['q' => 'What is used to inherit a class in Python?', 'a' => 'extend', 'b' => 'super()', 'c' => 'inherit', 'd' => 'The parent class name in parentheses', 'correct' => 4],
                ['q' => 'What keyword skips the rest of a loop iteration?', 'a' => 'skip', 'b' => 'pass', 'c' => 'continue', 'd' => 'jump', 'correct' => 3],
                ['q' => 'What does range(5) produce?', 'a' => '1 to 5', 'b' => '0 to 4', 'c' => '0 to 5', 'd' => '1 to 4', 'correct' => 2],
                ['q' => 'Which module is used for regular expressions in Python?', 'a' => 'regex', 'b' => 'rx', 'c' => 're', 'd' => 'regexp', 'correct' => 3],
                ['q' => 'What does "self" refer to in a Python class?', 'a' => 'The parent class', 'b' => 'The current instance of the class', 'c' => 'The method being called', 'd' => 'A global variable', 'correct' => 2],
                ['q' => 'What is a lambda function in Python?', 'a' => 'A recursive function', 'b' => 'An anonymous single-expression function', 'c' => 'A class method', 'd' => 'A built-in function', 'correct' => 2],
                ['q' => 'Which exception is raised for division by zero?', 'a' => 'ValueError', 'b' => 'TypeError', 'c' => 'ZeroDivisionError', 'd' => 'ArithmeticError', 'correct' => 3],
                ['q' => 'What does the __init__ method do?', 'a' => 'Deletes an object', 'b' => 'Initialises a class instance', 'c' => 'Returns class type', 'd' => 'Inherits a class', 'correct' => 2],
                ['q' => 'How do you open a file for reading in Python?', 'a' => "open('file', 'w')", 'b' => "open('file', 'a')", 'c' => "open('file', 'x')", 'd' => "open('file', 'r')", 'correct' => 4],
                ['q' => "What is the output of 'hello'.upper()?", 'a' => 'Hello', 'b' => 'HELLO', 'c' => 'hello', 'd' => 'hELLO', 'correct' => 2],
                ['q' => 'Which built-in function returns the largest value?', 'a' => 'large()', 'b' => 'top()', 'c' => 'max()', 'd' => 'highest()', 'correct' => 3],
                ['q' => 'What does list comprehension [x*2 for x in range(3)] produce?', 'a' => '[1,2,3]', 'b' => '[0,2,4]', 'c' => '[2,4,6]', 'd' => '[0,1,2]', 'correct' => 2],
                ['q' => 'What is the correct way to check if key exists in a dict?', 'a' => "key in dict.keys() only", 'b' => "dict.has(key)", 'c' => "key in dict", 'd' => "dict.find(key)", 'correct' => 3],
                ['q' => 'What does the "pass" statement do in Python?', 'a' => 'Exits the program', 'b' => 'Skips an iteration', 'c' => 'Does nothing — a no-op placeholder', 'd' => 'Returns None', 'correct' => 3],
                ['q' => 'Which sorting method does Python\'s built-in sort() use?', 'a' => 'QuickSort', 'b' => 'MergeSort', 'c' => 'TimSort', 'd' => 'HeapSort', 'correct' => 3],
                ['q' => 'What is a generator in Python?', 'a' => 'A class that generates objects', 'b' => 'A function using yield to produce a sequence lazily', 'c' => 'A random number tool', 'd' => 'A file reader', 'correct' => 2],
                ['q' => 'How do you convert a list to a set in Python?', 'a' => 'list.toSet()', 'b' => 'set(list)', 'c' => '{list}', 'd' => 'Set.from(list)', 'correct' => 2],
                ['q' => 'Which of the following is immutable in Python?', 'a' => 'List', 'b' => 'Dictionary', 'c' => 'Set', 'd' => 'Tuple', 'correct' => 4],
                ['q' => 'What is the difference between "==" and "is" in Python?', 'a' => 'No difference', 'b' => '"==" checks value equality; "is" checks identity (same object)', 'c' => '"is" checks value; "==" checks type', 'd' => '"is" is used for strings only', 'correct' => 2],
                ['q' => 'What does the zip() function do in Python?', 'a' => 'Compresses files', 'b' => 'Combines iterables element-wise into tuples', 'c' => 'Sorts a list', 'd' => 'Filters a list', 'correct' => 2],
                ['q' => 'Which decorator makes a method callable on the class itself?', 'a' => '@property', 'b' => '@abstractmethod', 'c' => '@staticmethod', 'd' => '@classmethod', 'correct' => 4],
                ['q' => 'What does the "with" statement provide in Python?', 'a' => 'Loop control', 'b' => 'Context management — ensures clean-up (e.g., file close)', 'c' => 'Exception catching', 'd' => 'Module importing', 'correct' => 2],
                ['q' => 'What is the output of bool("") in Python?', 'a' => 'True', 'b' => 'None', 'c' => 'Error', 'd' => 'False', 'correct' => 4],
            ];
            foreach ($pythonQs as $q) {
                QuestionBank::firstOrCreate(
                    ['course_id' => $course1->id, 'question_text' => $q['q']],
                    ['option_a' => $q['a'], 'option_b' => $q['b'], 'option_c' => $q['c'], 'option_d' => $q['d'], 'correct_option' => $q['correct'], 'difficulty' => 'medium']
                );
            }
        }

        // ── Course 2: Laravel ─────────────────────────────────────────────────
        if ($course2 && QuestionBank::where('course_id', $course2->id)->count() < 30) {
            $laravelQs = [
                ['q' => 'What command creates a new Laravel controller?', 'a' => 'php artisan new:controller', 'b' => 'php artisan make:controller', 'c' => 'laravel make controller', 'd' => 'php artisan create:controller', 'correct' => 2],
                ['q' => 'What does Eloquent ORM stand for?', 'a' => 'A query builder only', 'b' => 'Laravel\'s built-in Active Record ORM', 'c' => 'An external PHP library', 'd' => 'A caching system', 'correct' => 2],
                ['q' => 'Which method returns all records from an Eloquent model?', 'a' => 'find()', 'b' => 'get()', 'c' => 'all()', 'd' => 'fetch()', 'correct' => 3],
                ['q' => 'What file stores database connection settings in Laravel?', 'a' => 'config/session.php', 'b' => 'config/database.php', 'c' => '.env only', 'd' => 'bootstrap/app.php', 'correct' => 2],
                ['q' => 'What does "php artisan migrate" do?', 'a' => 'Creates a new database', 'b' => 'Exports data to CSV', 'c' => 'Runs pending database migrations', 'd' => 'Backs up the database', 'correct' => 3],
                ['q' => 'Which Blade directive outputs a variable safely?', 'a' => '{{ $var }}', 'b' => '{!! $var !!}', 'c' => '@echo($var)', 'd' => '<= $var =>', 'correct' => 1],
                ['q' => 'What is middleware in Laravel?', 'a' => 'A type of Eloquent model', 'b' => 'Code that runs between a request and response', 'c' => 'A Blade layout component', 'd' => 'A caching driver', 'correct' => 2],
                ['q' => 'How do you define a route in Laravel?', 'a' => 'In app/Http/Kernel.php', 'b' => 'In config/routes.php', 'c' => 'In routes/web.php', 'd' => 'In public/index.php', 'correct' => 3],
                ['q' => 'What does the "belongsTo" relationship define?', 'a' => 'One-to-many from the parent', 'b' => 'Many-to-many', 'c' => 'The inverse of hasMany', 'd' => 'A polymorphic link', 'correct' => 3],
                ['q' => 'What is a seeder used for in Laravel?', 'a' => 'Creating migrations', 'b' => 'Populating the database with sample data', 'c' => 'Generating controllers', 'd' => 'Building routes', 'correct' => 2],
                ['q' => 'Which artisan command creates a new migration file?', 'a' => 'php artisan db:migrate', 'b' => 'php artisan new:migration', 'c' => 'php artisan make:migration', 'd' => 'php artisan create:table', 'correct' => 3],
                ['q' => 'What does the @csrf Blade directive do?', 'a' => 'Encrypts the form data', 'b' => 'Adds a hidden CSRF token field to the form', 'c' => 'Validates the form', 'd' => 'Redirects after submit', 'correct' => 2],
                ['q' => 'What is a service provider in Laravel?', 'a' => 'A class for API calls', 'b' => 'The central place to bootstrap application services', 'c' => 'A database model', 'd' => 'A session driver', 'correct' => 2],
                ['q' => 'How do you access query string parameters in a Controller?', 'a' => '$_GET["param"]', 'b' => 'request()->input("param")', 'c' => 'Request::param()', 'd' => 'Input::get("param")', 'correct' => 2],
                ['q' => 'What does "php artisan route:list" display?', 'a' => 'All registered routes', 'b' => 'All controllers', 'c' => 'All middleware', 'd' => 'All models', 'correct' => 1],
                ['q' => 'What is a factory in Laravel?', 'a' => 'A class to create controllers automatically', 'b' => 'A class for generating fake model data in tests', 'c' => 'A Blade component', 'd' => 'A queue worker', 'correct' => 2],
                ['q' => 'Which Laravel facade is used to interact with the filesystem?', 'a' => 'DB', 'b' => 'Cache', 'c' => 'Storage', 'd' => 'File', 'correct' => 3],
                ['q' => 'What does "withCount" do in an Eloquent query?', 'a' => 'Limits query results', 'b' => 'Loads a relationship eagerly', 'c' => 'Adds a count of a related model to the result', 'd' => 'Groups results', 'correct' => 3],
                ['q' => 'What is eager loading in Eloquent?', 'a' => 'Caching query results', 'b' => 'Loading related models in a single query to avoid N+1', 'c' => 'Running queries asynchronously', 'd' => 'Paginating results', 'correct' => 2],
                ['q' => 'Which command publishes vendor assets in Laravel?', 'a' => 'php artisan vendor:publish', 'b' => 'php artisan publish:assets', 'c' => 'php artisan assets:install', 'd' => 'php artisan migrate:vendor', 'correct' => 1],
                ['q' => 'What does the "hasMany" relationship return?', 'a' => 'A single model', 'b' => 'A collection of related models', 'c' => 'A boolean', 'd' => 'An integer count', 'correct' => 2],
                ['q' => 'What is the purpose of the .env file in Laravel?', 'a' => 'Storing Blade templates', 'b' => 'Environment-specific configuration (keys, DB, etc.)', 'c' => 'PHP class autoloading', 'd' => 'Defining routes', 'correct' => 2],
                ['q' => 'Which Eloquent method finds a model by its primary key?', 'a' => 'get()', 'b' => 'first()', 'c' => 'where()', 'd' => 'find()', 'correct' => 4],
                ['q' => 'What does "php artisan db:seed" do?', 'a' => 'Resets all tables', 'b' => 'Runs all database seeders', 'c' => 'Creates new tables', 'd' => 'Backs up the database', 'correct' => 2],
                ['q' => 'What is the Blade templating engine?', 'a' => 'A CSS framework bundled with Laravel', 'b' => 'Laravel\'s built-in template engine for views', 'c' => 'A JavaScript compiler', 'd' => 'An external templating library', 'correct' => 2],
                ['q' => 'How do you create a named route in Laravel?', 'a' => 'Route::get()->name("name")', 'b' => 'Route::name("name")->get()', 'c' => 'Route::label("name", ...)', 'd' => 'Route::alias("name", ...)', 'correct' => 1],
                ['q' => 'What does "php artisan config:cache" do?', 'a' => 'Clears the route cache', 'b' => 'Combines all config files into a single cached file for speed', 'c' => 'Publishes config files', 'd' => 'Resets environment variables', 'correct' => 2],
                ['q' => 'What is a pivot table in Laravel?', 'a' => 'A log table', 'b' => 'A table used to manage many-to-many relationships', 'c' => 'An Eloquent model without a table', 'd' => 'A cache store', 'correct' => 2],
                ['q' => 'Which method sends a JSON response from a Laravel controller?', 'a' => 'return view()->json()', 'b' => 'return json($data)', 'c' => 'return response()->json($data)', 'd' => 'return output()->json($data)', 'correct' => 3],
                ['q' => 'What does the "firstOrCreate" Eloquent method do?', 'a' => 'Always creates a new record', 'b' => 'Returns the first matching record or creates it if not found', 'c' => 'Finds by primary key only', 'd' => 'Updates an existing record', 'correct' => 2],
            ];
            foreach ($laravelQs as $q) {
                QuestionBank::firstOrCreate(
                    ['course_id' => $course2->id, 'question_text' => $q['q']],
                    ['option_a' => $q['a'], 'option_b' => $q['b'], 'option_c' => $q['c'], 'option_d' => $q['d'], 'correct_option' => $q['correct'], 'difficulty' => 'medium']
                );
            }
        }

        // Always ensure prerequisite: Python before Laravel
        if ($course1 && $course2) {
            CoursePrerequisite::firstOrCreate([
                'course_id' => $course2->id,
                'prerequisite_course_id' => $course1->id,
            ]);
        }

        // ── Course 3: Data Structures & Algorithms ────────────────────────────
        if ($course3 && QuestionBank::where('course_id', $course3->id)->count() < 30) {
            $dsaQs = [
                ['q' => 'What data structure uses LIFO ordering?', 'a' => 'Queue', 'b' => 'Linked List', 'c' => 'Stack', 'd' => 'Tree', 'correct' => 3],
                ['q' => 'What is the time complexity of accessing an element in an array?', 'a' => 'O(n)', 'b' => 'O(log n)', 'c' => 'O(n²)', 'd' => 'O(1)', 'correct' => 4],
                ['q' => 'Which traversal visits root, left, then right in a binary tree?', 'a' => 'Inorder', 'b' => 'Preorder', 'c' => 'Postorder', 'd' => 'Level-order', 'correct' => 2],
                ['q' => 'What is Big O notation used for?', 'a' => 'Measuring memory size', 'b' => 'Describing algorithm efficiency as input grows', 'c' => 'Compiling code faster', 'd' => 'Sorting databases', 'correct' => 2],
                ['q' => 'What is the worst-case time complexity of Bubble Sort?', 'a' => 'O(n log n)', 'b' => 'O(log n)', 'c' => 'O(n)', 'd' => 'O(n²)', 'correct' => 4],
                ['q' => 'A queue follows which principle?', 'a' => 'LIFO', 'b' => 'FIFO', 'c' => 'FILO', 'd' => 'LILO', 'correct' => 2],
                ['q' => 'What is the height of a balanced binary tree with n nodes?', 'a' => 'O(n)', 'b' => 'O(n²)', 'c' => 'O(1)', 'd' => 'O(log n)', 'correct' => 4],
                ['q' => 'Which sorting algorithm has O(n log n) worst-case complexity?', 'a' => 'Quick Sort', 'b' => 'Bubble Sort', 'c' => 'Merge Sort', 'd' => 'Insertion Sort', 'correct' => 3],
                ['q' => 'What is a hash collision?', 'a' => 'When two keys produce the same hash value', 'b' => 'When a hash table is full', 'c' => 'When two values are identical', 'd' => 'A hash function error', 'correct' => 1],
                ['q' => 'Which data structure is best for implementing a breadth-first search?', 'a' => 'Stack', 'b' => 'Array', 'c' => 'Queue', 'd' => 'Linked List', 'correct' => 3],
                ['q' => 'What is the purpose of a sentinel node in a linked list?', 'a' => 'To store extra data', 'b' => 'A dummy node to simplify edge-case handling', 'c' => 'A node that marks the end', 'd' => 'A node for sorting', 'correct' => 2],
                ['q' => 'Dynamic programming solves problems by...?', 'a' => 'Random sampling', 'b' => 'Brute force', 'c' => 'Greedy selection', 'd' => 'Breaking them into overlapping subproblems and caching results', 'correct' => 4],
                ['q' => 'What is a directed acyclic graph (DAG)?', 'a' => 'A graph with no vertices', 'b' => 'A graph with directed edges and no cycles', 'c' => 'An undirected graph', 'd' => 'A balanced tree', 'correct' => 2],
                ['q' => 'Which operation is O(1) in a hash map?', 'a' => 'Sorting', 'b' => 'Searching for a key', 'c' => 'Iterating all keys', 'd' => 'Finding maximum', 'correct' => 2],
                ['q' => 'What is space complexity?', 'a' => 'How much time an algorithm takes', 'b' => 'The amount of memory an algorithm uses relative to input size', 'c' => 'The number of iterations', 'd' => 'The code length', 'correct' => 2],
                ['q' => 'Which tree guarantees O(log n) operations by self-balancing?', 'a' => 'Binary Tree', 'b' => 'B-Tree', 'c' => 'AVL Tree', 'd' => 'Trie', 'correct' => 3],
                ['q' => 'What is a trie data structure used for?', 'a' => 'Graph traversal', 'b' => 'Efficient string prefix searching', 'c' => 'Sorting numbers', 'd' => 'Heap operations', 'correct' => 2],
                ['q' => 'What is the recurrence relation of Merge Sort?', 'a' => 'T(n) = T(n-1) + O(n)', 'b' => 'T(n) = 2T(n/2) + O(n)', 'c' => 'T(n) = T(n/2) + O(1)', 'd' => 'T(n) = T(n-1) + O(1)', 'correct' => 2],
                ['q' => 'What property must a min-heap satisfy?', 'a' => 'Every parent is larger than its children', 'b' => 'Every parent is smaller than or equal to its children', 'c' => 'All leaves are at the same level', 'd' => 'The tree must be balanced', 'correct' => 2],
                ['q' => 'Which problem-solving paradigm does Dijkstra\'s algorithm use?', 'a' => 'Divide and Conquer', 'b' => 'Dynamic Programming', 'c' => 'Backtracking', 'd' => 'Greedy', 'correct' => 4],
                ['q' => 'What is a doubly linked list?', 'a' => 'A list with two heads', 'b' => 'A list where each node has pointers to both next and previous nodes', 'c' => 'A list with two tails', 'd' => 'A circular linked list', 'correct' => 2],
                ['q' => "What is Kadane's algorithm used for?", 'a' => 'Finding the shortest path', 'b' => 'Maximum subarray sum', 'c' => 'Sorting arrays', 'd' => 'Tree traversal', 'correct' => 2],
                ['q' => 'What does BFS stand for?', 'a' => 'Binary File Search', 'b' => 'Best First Search', 'c' => 'Breadth-First Search', 'd' => 'Balanced Function Sort', 'correct' => 3],
                ['q' => 'What is the purpose of a priority queue?', 'a' => 'To store items in insertion order', 'b' => 'To serve elements with highest priority first', 'c' => 'To allow random access', 'd' => 'To sort items alphabetically', 'correct' => 2],
                ['q' => 'In a binary search, what is the key requirement for the input?', 'a' => 'The array must be unsorted', 'b' => 'The array must be sorted', 'c' => 'The array must have an odd number of elements', 'd' => 'The array must have no duplicates', 'correct' => 2],
                ['q' => 'What is the time complexity of binary search?', 'a' => 'O(n)', 'b' => 'O(n²)', 'c' => 'O(1)', 'd' => 'O(log n)', 'correct' => 4],
                ['q' => 'What is a circular linked list?', 'a' => 'A list with no nodes', 'b' => 'A list where the last node points back to the first', 'c' => 'A doubly linked list', 'd' => 'A list stored in a circle on disk', 'correct' => 2],
                ['q' => 'What is the best-case time complexity of QuickSort?', 'a' => 'O(n²)', 'b' => 'O(n)', 'c' => 'O(n log n)', 'd' => 'O(log n)', 'correct' => 3],
                ['q' => 'In graph theory, what is a spanning tree?', 'a' => 'A tree that includes all vertices of the graph with minimum edges', 'b' => 'The longest path in a graph', 'c' => 'A cycle-free subgraph', 'd' => 'A directed path', 'correct' => 1],
                ['q' => 'What does DFS stand for?', 'a' => 'Direct Function Search', 'b' => 'Data Field Structure', 'c' => 'Depth-First Search', 'd' => 'Dynamic File Scan', 'correct' => 3],
            ];
            foreach ($dsaQs as $q) {
                QuestionBank::firstOrCreate(
                    ['course_id' => $course3->id, 'question_text' => $q['q']],
                    ['option_a' => $q['a'], 'option_b' => $q['b'], 'option_c' => $q['c'], 'option_d' => $q['d'], 'correct_option' => $q['correct'], 'difficulty' => 'medium']
                );
            }
        }

        // ── Course 4: Machine Learning ────────────────────────────────────────
        if ($course4 && QuestionBank::where('course_id', $course4->id)->count() < 30) {
            $mlQs = [
                ['q' => 'What is supervised learning?', 'a' => 'Learning without any labels', 'b' => 'Learning where the model is trained on labelled input-output pairs', 'c' => 'Reinforcement-based training', 'd' => 'Clustering unlabeled data', 'correct' => 2],
                ['q' => 'What is the loss function in machine learning?', 'a' => 'The accuracy score', 'b' => 'A measure of how wrong the model predictions are', 'c' => 'The number of training epochs', 'd' => 'The learning rate', 'correct' => 2],
                ['q' => 'What does gradient descent do?', 'a' => 'Finds the maximum of the loss function', 'b' => 'Randomly selects model parameters', 'c' => 'Iteratively minimises the loss function by updating weights', 'd' => 'Normalises the input data', 'correct' => 3],
                ['q' => 'What is a training set?', 'a' => 'Data used to evaluate final model performance', 'b' => 'Data used to tune hyperparameters', 'c' => 'Data withheld for final testing', 'd' => 'Data used to fit the model', 'correct' => 4],
                ['q' => 'What is regularisation in machine learning?', 'a' => 'Standardising input features', 'b' => 'A technique to prevent overfitting by penalising large weights', 'c' => 'Increasing model complexity', 'd' => 'Sorting training data', 'correct' => 2],
                ['q' => 'What does a confusion matrix show?', 'a' => 'Loss per epoch', 'b' => 'Feature importance', 'c' => 'True positives, false positives, true negatives, and false negatives', 'd' => 'Learning rate curves', 'correct' => 3],
                ['q' => 'What is the purpose of the activation function in a neural network?', 'a' => 'To initialise weights', 'b' => 'To introduce non-linearity into the network', 'c' => 'To normalise the output', 'd' => 'To reduce training time', 'correct' => 2],
                ['q' => 'What is k-fold cross validation?', 'a' => 'Training k separate models', 'b' => 'Using k features only', 'c' => 'Splitting data into k folds and training/validating k times', 'd' => 'A hyperparameter search method', 'correct' => 3],
                ['q' => 'What does the ReLU activation function do?', 'a' => 'Returns values between 0 and 1', 'b' => 'Returns the input if positive, else zero', 'c' => 'Applies a sigmoid curve', 'd' => 'Normalises to -1 to 1', 'correct' => 2],
                ['q' => 'What is a hyperparameter in ML?', 'a' => 'A learned model weight', 'b' => 'A parameter set before training that controls learning', 'c' => 'An input feature', 'd' => 'A performance metric', 'correct' => 2],
                ['q' => 'What is the difference between classification and regression?', 'a' => 'No difference', 'b' => 'Classification predicts discrete labels; regression predicts continuous values', 'c' => 'Regression uses decision trees; classification uses neural networks', 'd' => 'Classification is unsupervised; regression is supervised', 'correct' => 2],
                ['q' => 'What is a decision tree?', 'a' => 'A neural network architecture', 'b' => 'A graph-based model', 'c' => 'A tree-like model that splits data based on feature values', 'd' => 'A clustering algorithm', 'correct' => 3],
                ['q' => 'What is precision in a classification model?', 'a' => 'True positives / (True positives + False negatives)', 'b' => 'True positives / (True positives + False positives)', 'c' => 'Total correct / Total samples', 'd' => 'True negatives / Total negatives', 'correct' => 2],
                ['q' => 'What is a support vector machine (SVM)?', 'a' => 'A clustering algorithm', 'b' => 'A supervised algorithm that finds an optimal separating hyperplane', 'c' => 'A dimensionality reduction method', 'd' => 'A type of recurrent neural network', 'correct' => 2],
                ['q' => 'What is the bias-variance tradeoff?', 'a' => 'The balance between learning rate and batch size', 'b' => 'The tradeoff between underfitting (high bias) and overfitting (high variance)', 'c' => 'The ratio of training to test data', 'd' => 'A regularisation technique', 'correct' => 2],
                ['q' => 'What is backpropagation?', 'a' => 'Forward pass through a network', 'b' => 'Algorithm to compute gradients by propagating errors backward through layers', 'c' => 'A type of data augmentation', 'd' => 'A weight initialisation method', 'correct' => 2],
                ['q' => 'Which algorithm is used for dimensionality reduction?', 'a' => 'Random Forest', 'b' => 'PCA (Principal Component Analysis)', 'c' => 'Logistic Regression', 'd' => 'K-Means Clustering', 'correct' => 2],
                ['q' => 'What does an epoch mean in neural network training?', 'a' => 'One forward pass', 'b' => 'One complete pass through the entire training dataset', 'c' => 'One gradient update', 'd' => 'One test evaluation', 'correct' => 2],
                ['q' => 'What is unsupervised learning?', 'a' => 'Learning with labelled data', 'b' => 'Learning with human feedback at each step', 'c' => 'Learning patterns from unlabelled data', 'd' => 'Reinforcement learning', 'correct' => 3],
                ['q' => 'What is the softmax function used for?', 'a' => 'Binary classification output', 'b' => 'Converting raw scores into probabilities summing to 1 for multiclass', 'c' => 'Normalising input features', 'd' => 'Applying dropout', 'correct' => 2],
                ['q' => 'What is transfer learning?', 'a' => 'Training from scratch on a new dataset', 'b' => 'Using a pre-trained model as a starting point for a new task', 'c' => 'Copying model weights to another language', 'd' => 'Moving data between servers', 'correct' => 2],
                ['q' => 'What is a batch in gradient descent?', 'a' => 'The total training set', 'b' => 'A single data point', 'c' => 'A subset of training data used for one gradient update', 'd' => 'The validation set', 'correct' => 3],
                ['q' => 'What is dropout in neural networks?', 'a' => 'Removing layers', 'b' => 'Randomly deactivating neurons during training to prevent overfitting', 'c' => 'Reducing the learning rate', 'd' => 'Skipping certain epochs', 'correct' => 2],
                ['q' => 'What is the F1 score?', 'a' => 'Accuracy × Recall', 'b' => 'Precision + Recall', 'c' => 'Harmonic mean of Precision and Recall', 'd' => 'Accuracy on the test set', 'correct' => 3],
                ['q' => 'What is a random forest?', 'a' => 'A single very deep decision tree', 'b' => 'An ensemble of decision trees trained on random subsets', 'c' => 'A clustering algorithm', 'd' => 'A neural network with random weights', 'correct' => 2],
                ['q' => 'What is overfitting in machine learning?', 'a' => 'When a model performs poorly on training data', 'b' => 'When a model learns training data too well and fails to generalise', 'c' => 'When the learning rate is too low', 'd' => 'When the dataset is too large', 'correct' => 2],
                ['q' => 'What is the purpose of a validation set?', 'a' => 'To train the model', 'b' => 'To tune hyperparameters and evaluate during training without using test data', 'c' => 'To augment data', 'd' => 'To normalise features', 'correct' => 2],
                ['q' => 'What does KNN stand for?', 'a' => 'Kernel Node Network', 'b' => 'K Nearest Neighbours', 'c' => 'K-means Neural Network', 'd' => 'Keypoint Node Normaliser', 'correct' => 2],
                ['q' => 'What is feature scaling?', 'a' => 'Reducing the number of features', 'b' => 'Normalising feature ranges so no feature dominates due to magnitude', 'c' => 'Selecting the most important features', 'd' => 'Encoding categorical variables', 'correct' => 2],
                ['q' => 'What is a ROC curve used for?', 'a' => 'Plotting training loss over epochs', 'b' => 'Visualising the tradeoff between true positive rate and false positive rate', 'c' => 'Showing feature correlations', 'd' => 'Comparing multiple regression lines', 'correct' => 2],
            ];
            foreach ($mlQs as $q) {
                QuestionBank::firstOrCreate(
                    ['course_id' => $course4->id, 'question_text' => $q['q']],
                    ['option_a' => $q['a'], 'option_b' => $q['b'], 'option_c' => $q['c'], 'option_d' => $q['d'], 'correct_option' => $q['correct'], 'difficulty' => 'medium']
                );
            }
        }

        // Always ensure prerequisite: DSA before ML
        if ($course3 && $course4) {
            CoursePrerequisite::firstOrCreate([
                'course_id' => $course4->id,
                'prerequisite_course_id' => $course3->id,
            ]);
        }

        $this->command->info('✅ QuestionBankSeeder complete.');
    }
}
