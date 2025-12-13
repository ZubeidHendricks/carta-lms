import { Accordion } from '@/components/ui/accordion';
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { getRouteSegments } from '@/lib/route';
import { cn } from '@/lib/utils';
import { SharedData } from '@/types/global';
import { usePage } from '@inertiajs/react';
import { GitCompareArrows } from 'lucide-react';
import { useEffect, useState } from 'react';
import NavMainItem from './nav-main-item';
import routes from './routes';

export function NavMain() {
   const page = usePage<SharedData>();
   const { auth, system } = page.props;
   const [openAccordions, setOpenAccordions] = useState<string>('');

   // Debug logging
   console.log('NavMain Debug:', {
      userRole: auth.user?.role,
      systemSubType: system.sub_type,
      systemFields: system.fields
   });

   // Set initial accordion state based on URL
   useEffect(() => {
      const slug = getRouteSegments(page.url);

      if (slug.length > 1) {
         setOpenAccordions(slug[1]);
      }
   }, [page.url]);

   return (
      <SidebarGroup className="px-2 py-0">
         <Accordion type="single" collapsible value={openAccordions} defaultValue={openAccordions} onValueChange={setOpenAccordions}>
            {routes.map(({ title, pages }, key) => {
               const visiblePages = pages.filter((page) => {
                  const role = page.access.includes(auth.user?.role || 'admin');
                  const subType = page.access.includes(system.sub_type || 'collaborative');
                  console.log(`Page: ${page.name}, Role Check: ${role}, SubType Check: ${subType}, Access: ${page.access.join(',')}`);
                  return role && subType;
               });

               // Only render section if it has visible pages
               if (visiblePages.length === 0) {
                  return null;
               }

               return (
               <SidebarMenu key={key} className="space-y-1">
                  <SidebarGroupLabel>{title}</SidebarGroupLabel>

                  {visiblePages.map((page) => (
                     <SidebarMenuItem key={page.slug}>
                        <NavMainItem pageRoute={page} />
                     </SidebarMenuItem>
                  ))}
               </SidebarMenu>
            );
            })}
            
            <SidebarMenu className="space-y-1">
               <SidebarMenuItem>
                  <SidebarMenuButton asChild className={cn('h-9')}>
                     <a target="_blank" href={route('system.maintenance')}>
                        <GitCompareArrows className="h-4 w-4" />
                        <span>Maintenance</span>
                     </a>
                  </SidebarMenuButton>
               </SidebarMenuItem>
            </SidebarMenu>
         </Accordion>
      </SidebarGroup>
   );
}
