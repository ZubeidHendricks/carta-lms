import { cn } from '@/lib/utils';
import { SharedData } from '@/types/global';
import { usePage } from '@inertiajs/react';

const AppLogo = ({ className, theme }: { theme?: 'light' | 'dark'; className?: string }) => {
   const { system } = usePage<SharedData>().props;

   if (theme && theme === 'dark') {
      return <span className={cn('block text-2xl font-bold text-white', className)}>CARTA</span>;
   }

   if (theme && theme === 'light') {
      return <span className={cn('block text-2xl font-bold text-gray-900', className)}>CARTA</span>;
   }

   return (
      <>
         <span className={cn('block text-2xl font-bold text-gray-900 dark:hidden', className)}>CARTA</span>
         <span className={cn('hidden text-2xl font-bold text-white dark:block', className)}>CARTA</span>
      </>
   );
};

export default AppLogo;
