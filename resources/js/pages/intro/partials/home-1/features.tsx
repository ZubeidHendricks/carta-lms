import { Card } from '@/components/ui/card';
import { GraduationCap, Bot, BarChart3, Video, FileText, DollarSign, Users, Award, Clock } from 'lucide-react';

const Features = () => {
   const features = [
      {
         icon: GraduationCap,
         title: 'Interactive Courses',
         description: 'Create engaging courses with video lessons, quizzes, assignments, and rich multimedia content'
      },
      {
         icon: Bot,
         title: 'AI-Powered Tutoring',
         description: 'Get instant help from AI tutors powered by Tavus interactive video technology'
      },
      {
         icon: BarChart3,
         title: 'Progress Tracking',
         description: 'Monitor student progress with detailed analytics, gamification, and performance insights'
      },
      {
         icon: Video,
         title: 'Live Classes',
         description: 'Conduct live virtual classes with Zoom integration and real-time interaction'
      },
      {
         icon: FileText,
         title: 'Exams & Certificates',
         description: 'Create comprehensive exams and issue professional certificates upon completion'
      },
      {
         icon: DollarSign,
         title: 'Monetization',
         description: 'Sell courses with integrated payment gateways and flexible pricing options'
      },
      {
         icon: Users,
         title: 'Collaborative Learning',
         description: 'Enable discussions, forums, and peer-to-peer learning experiences'
      },
      {
         icon: Award,
         title: 'Gamification',
         description: 'Badges, leaderboards, and points system to motivate and engage learners'
      },
      {
         icon: Clock,
         title: 'Drip Content',
         description: 'Schedule content release to create structured learning paths'
      }
   ];

   return (
      <section className="py-20 bg-background">
         <div className="container mx-auto px-4">
            <div className="text-center mb-12">
               <h2 className="text-4xl font-bold mb-4">Powerful Features for Modern Learning</h2>
               <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                  Everything you need to create, manage, and deliver exceptional online learning experiences
               </p>
            </div>
            
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
               {features.map((feature, index) => {
                  const Icon = feature.icon;
                  return (
                     <Card key={index} className="p-6 hover:shadow-lg transition-shadow">
                        <div className="flex items-start gap-4">
                           <div className="flex-shrink-0">
                              <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                                 <Icon className="w-6 h-6 text-primary" />
                              </div>
                           </div>
                           <div className="flex-1">
                              <h3 className="text-lg font-semibold mb-2">{feature.title}</h3>
                              <p className="text-sm text-muted-foreground">{feature.description}</p>
                           </div>
                        </div>
                     </Card>
                  );
               })}
            </div>
         </div>
      </section>
   );
};

export default Features;
