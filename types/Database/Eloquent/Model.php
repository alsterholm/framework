<?php

namespace Illuminate\Types\Model;

use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\QueriedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\HasCollection;
use Illuminate\Database\Eloquent\Model;
use User;

use function PHPStan\Testing\assertType;

function test(User $user, Post $post, Comment $comment, Article $article): void
{
    assertType('UserFactory', User::factory(function ($attributes, $model) {
        assertType('array<string, mixed>', $attributes);
        assertType('User|null', $model);

        return ['string' => 'string'];
    }));
    assertType('UserFactory', User::factory(42, function ($attributes, $model) {
        assertType('array<string, mixed>', $attributes);
        assertType('User|null', $model);

        return ['string' => 'string'];
    }));

    assertType('Illuminate\Database\Eloquent\Builder<User>', User::query());
    assertType('Illuminate\Database\Eloquent\Builder<User>', $user->newQuery());
    assertType('Illuminate\Database\Eloquent\Builder<User>', $user->withTrashed());
    assertType('Illuminate\Database\Eloquent\Builder<User>', $user->onlyTrashed());
    assertType('Illuminate\Database\Eloquent\Builder<User>', $user->withoutTrashed());
    assertType('Illuminate\Database\Eloquent\Builder<User>', $user->prunable());
    assertType('Illuminate\Database\Eloquent\Relations\MorphMany<Illuminate\Notifications\DatabaseNotification, User>', $user->notifications());
    assertType('Illuminate\Types\Model\ArticleBuilder<Illuminate\Types\Model\Article>', Article::query());

    assertType('Illuminate\Database\Eloquent\Collection<(int|string), User>', $user->newCollection([new User()]));
    assertType('Illuminate\Types\Model\Posts<(int|string), Illuminate\Types\Model\Post>', $post->newCollection(['foo' => new Post()]));
    assertType('Illuminate\Types\Model\Articles<(int|string), Illuminate\Types\Model\Article>', $article->newCollection([new Article()]));
    assertType('Illuminate\Types\Model\Comments', $comment->newCollection([new Comment()]));

    assertType('bool', $user->restore());
    assertType('User', $user->restoreOrCreate());
    assertType('User', $user->createOrRestore());
}

class Post extends Model
{
    /** @use HasCollection<Posts<array-key, static>> */
    use HasCollection;

    protected static string $collectionClass = Posts::class;
}

/**
 * @template TKey of array-key
 * @template TModel of Post
 *
 * @extends Collection<TKey, TModel> */
class Posts extends Collection
{
}

final class Comment extends Model
{
    /** @use HasCollection<Comments> */
    use HasCollection;

    /** @param  array<array-key, Comment>  $models */
    public function newCollection(array $models = []): Comments
    {
        return new Comments($models);
    }
}

/** @extends Collection<array-key, Comment> */
final class Comments extends Collection
{
}

#[QueriedBy(ArticleBuilder::class)]
#[CollectedBy(Articles::class)]
class Article extends Model
{
    /** @use HasCollection<Articles<array-key, static>> */
    use HasCollection;

    /** @use HasBuilder<ArticleBuilder<static>> */
    use HasBuilder;
}

/**
 * @template TKey of array-key
 * @template TModel of Article
 *
 * @extends Collection<TKey, TModel> */
class Articles extends Collection
{
}

/**
 * @template TModel of Article
 *
 * @extends Builder<TModel> */
class ArticleBuilder extends Builder
{
}
