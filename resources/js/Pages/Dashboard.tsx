import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useViewportSize } from '@mantine/hooks';
import { useEffect, useRef, useState } from 'react';

export default function Dashboard() {
    const { height, width }: {
        height: number;
        width: number;
    } = useViewportSize();

    const [defaultRatio, setDefaultRatio] = useState(1 / 3);

    const onMouseMove = (e: PointerEvent) => {
        const newRatio = e.clientX / width;
        if (newRatio < 0.2 || newRatio > 0.7) return;
        setDefaultRatio(newRatio);
    };
    const onMouseUp = () => {
        window.removeEventListener('pointermove', onMouseMove);
        window.removeEventListener('pointerup', onMouseUp);
    };

    return (
        <AuthenticatedLayout>
            <Head title="File Manager" />

            <div className='w-screen h-full'>
                <div className='relative flex h-full'>
                    <div className='bg-gray-200 dark:bg-gray-800 dark:text-gray-200 h-full'
                        style={{
                            width: width * defaultRatio,
                        }}
                    >

                    </div>
                    <div className='group/resizeBar absolute top-0 bg-gray-300 hover:bg-gray-400 dark:bg-gray-800 z-10 h-full'
                        style={{
                            cursor: 'ew-resize',
                            userSelect: 'none',
                            left: `calc(${width * defaultRatio}px - 0.27rem)`,
                            width: '0.55rem',
                        }}
                        onPointerDown={() => {
                            window.addEventListener('pointermove', onMouseMove);
                            window.addEventListener('pointerup', onMouseUp);
                        }}
                        onDoubleClick={() => setDefaultRatio(1 / 3)}
                    >
                        <div className='flex flex-col gap-y-1 items-center justify-center w-full h-full'>
                            {
                                Array.from({ length: 3 }).map((_, i) => (<div className='w-1.5 h-1.5 bg-gray-500 group-hover/resizeBar:bg-gray-200 rounded-full transition-all ease-in-out'></div>))
                            }
                        </div>
                    </div>
                    <div className='bg-orange-200 dark:bg-gray-800 dark:text-gray-200 h-full'
                        style={{
                            width: width * (1 - defaultRatio),
                        }}
                    >

                    </div>
                </div>
            </div>
        </AuthenticatedLayout >
    );
}
