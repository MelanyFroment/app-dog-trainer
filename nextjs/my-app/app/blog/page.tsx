// @ts-ignore
import { getPosts } from '@/lib/posts'
// @ts-ignore
import { Post } from '@/ui/post'

export default async function Page() {
    const posts = await getPosts()

    return (
        <ul>
            {posts.map((post: { id: any }) => (
                <Post key={post.id} post={post} />
            ))}
        </ul>
    )
}