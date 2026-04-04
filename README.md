Steps for setup:
    1.composer update
    2.copy .env.example .env
    3.php artisan key:generate
    4.change database name in .env to melody_hub_studio
    5.php artisan migrate
    6.npm i && npm run dev 

After Setup:
    1.create a branch. name should be given according to task assigned.(task is assigned in trello)
    2.after task complete push code in your branch than drag your card to ready to merge.
    3.in trello if you have added new table or installed new packages then add instruction for others like php artisan migrate, npm i or composer update.
    4.if your are team leader 
        a.in terminal run git pull
        b.go to his/her branch
        c.check if everything is correct
        d.then merge to main and push
    5.others will pull code from main branch and follow step 3 instruction.
    6.no one is allowed to change code or in main branch and go to other team mates branch and make changes.

.......
