<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new AnswerDB);
Artisan::add(new QuestionTypeDB);
Artisan::add(new QuestionDB);
Artisan::add(new QuestionAnswerDB);
Artisan::add(new LocationDB);
Artisan::add(new TourDB);
Artisan::add(new FactorDB);
Artisan::add(new InteractionDB);
Artisan::add(new TourScoreDB);
Artisan::add(new DBTransaction());
Artisan::add(new AnswerMap());
Artisan::add(new UpdateInteraction());