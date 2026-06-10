import { IntroPageProps } from '@/types/page';
import { Head } from '@inertiajs/react';
import Layout from './partials/layout';
import TalentLanding from './partials/talent-landing';

/**
 * Home theme 1 — redesigned as a modern SaaS marketing landing page
 * (talentlms.com style). The previous data-driven section switch is replaced
 * by a bespoke, self-contained <TalentLanding /> so the page looks consistent
 * regardless of catalogue data. Nav + footer still come from <Layout>.
 */
const Home1 = ({ system }: IntroPageProps) => {
   return (
      <Layout>
         <Head title={system?.fields?.name || 'Carter LMS'} />
         <TalentLanding />
      </Layout>
   );
};

export default Home1;
