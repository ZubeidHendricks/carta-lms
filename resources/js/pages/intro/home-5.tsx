import { IntroPageProps } from '@/types/page';
import { Head } from '@inertiajs/react';
import Layout from './partials/layout';
import TalentLanding from './partials/talent-landing';

// Redesigned as the modern SaaS marketing landing (see talent-landing.tsx).
const Home5 = ({ system }: IntroPageProps) => {
   return (
      <Layout>
         <Head title={system?.fields?.name || 'Carter LMS'} />
         <TalentLanding />
      </Layout>
   );
};

export default Home5;
