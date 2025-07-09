import React from 'react'
import Hero from '../components/Hero'
import RouterCard from '../components/RouterCard'
import PricingCard from '../components/PricingCard'
import { 
  Wifi, 
  Globe, 
  Shield, 
  Clock, 
  Users, 
  Award,
  ArrowRight,
  Star,
  Quote
} from 'lucide-react'
import { motion } from 'framer-motion'
import { Link } from 'react-router-dom'

const Home: React.FC = () => {
  // Sample data - in real app, this would come from API
  const featuredRouters = [
    {
      id: '1',
      name: 'Starlink Standard',
      model: 'Gen 2',
      description: 'Perfect for residential use and small businesses. Reliable high-speed internet anywhere.',
      daily_rate: 25,
      monthly_rate: 599,
      deposit_required: 500,
      max_speed: '150 Mbps',
      coverage_area: '50km radius',
      features: ['Easy Setup', 'Weather Resistant', '24/7 Support'],
      image_url: 'https://images.pexels.com/photos/4792728/pexels-photo-4792728.jpeg?auto=compress&cs=tinysrgb&w=800',
      status: 'available' as const,
      rating: 4.8,
      reviews: 124
    },
    {
      id: '2',
      name: 'Starlink Business',
      model: 'High Performance',
      description: 'Enterprise-grade solution with priority data and enhanced performance.',
      daily_rate: 45,
      monthly_rate: 1099,
      deposit_required: 1000,
      max_speed: '350 Mbps',
      coverage_area: '100km radius',
      features: ['Priority Data', 'Enhanced Performance', 'Business Support'],
      image_url: 'https://images.pexels.com/photos/4792729/pexels-photo-4792729.jpeg?auto=compress&cs=tinysrgb&w=800',
      status: 'available' as const,
      rating: 4.9,
      reviews: 89
    },
    {
      id: '3',
      name: 'Starlink Mobile',
      model: 'Portable',
      description: 'Compact and portable solution for travelers and mobile applications.',
      daily_rate: 35,
      monthly_rate: 799,
      deposit_required: 750,
      max_speed: '200 Mbps',
      coverage_area: '75km radius',
      features: ['Portable Design', 'Quick Setup', 'Travel Friendly'],
      image_url: 'https://images.pexels.com/photos/4792730/pexels-photo-4792730.jpeg?auto=compress&cs=tinysrgb&w=800',
      status: 'rented' as const,
      rating: 4.7,
      reviews: 156
    }
  ]

  const pricingPlans = [
    {
      id: 'basic',
      name: 'Basic Plan',
      description: 'Perfect for short-term needs and testing',
      price: 25,
      period: 'day',
      features: [
        'Standard Starlink Router',
        'Up to 150 Mbps speed',
        'Basic setup support',
        'Standard customer service',
        'Flexible daily rental'
      ],
      buttonText: 'Start Basic Plan',
      buttonVariant: 'secondary' as const
    },
    {
      id: 'professional',
      name: 'Professional',
      description: 'Most popular for businesses and extended use',
      price: 599,
      period: 'month',
      originalPrice: 750,
      popular: true,
      features: [
        'High-performance router',
        'Up to 350 Mbps speed',
        'Priority customer support',
        'Free installation service',
        'Monthly billing discount',
        'Equipment insurance included'
      ],
      buttonText: 'Choose Professional',
      buttonVariant: 'primary' as const
    },
    {
      id: 'enterprise',
      name: 'Enterprise',
      description: 'Custom solutions for large organizations',
      price: 1299,
      period: 'month',
      features: [
        'Multiple router deployment',
        'Dedicated account manager',
        '24/7 priority support',
        'Custom SLA agreements',
        'Advanced monitoring tools',
        'Bulk pricing discounts'
      ],
      buttonText: 'Contact Sales',
      buttonVariant: 'secondary' as const
    }
  ]

  const features = [
    {
      icon: Wifi,
      title: 'High-Speed Internet',
      description: 'Get blazing fast satellite internet with speeds up to 350 Mbps, perfect for streaming, gaming, and business applications.'
    },
    {
      icon: Globe,
      title: 'Global Coverage',
      description: 'Access high-speed internet anywhere in the world with Starlink\'s advanced satellite constellation.'
    },
    {
      icon: Shield,
      title: 'Reliable & Secure',
      description: 'Enterprise-grade security and 99.9% uptime guarantee ensure your connection is always protected and available.'
    },
    {
      icon: Clock,
      title: 'Quick Setup',
      description: 'Get online in minutes with our easy plug-and-play setup. No technical expertise required.'
    },
    {
      icon: Users,
      title: '24/7 Support',
      description: 'Our expert support team is available around the clock to help with any questions or issues.'
    },
    {
      icon: Award,
      title: 'Premium Quality',
      description: 'All our equipment is regularly maintained and updated to ensure optimal performance and reliability.'
    }
  ]

  const testimonials = [
    {
      id: 1,
      name: 'Sarah Johnson',
      role: 'Remote Worker',
      company: 'Tech Startup',
      content: 'StarRent saved my remote work setup! The internet speed is incredible and the setup was so easy. Highly recommend for anyone working from remote locations.',
      rating: 5,
      avatar: 'https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=150'
    },
    {
      id: 2,
      name: 'Mike Chen',
      role: 'Event Organizer',
      company: 'EventPro',
      content: 'We used StarRent for our outdoor festival and it was perfect. Reliable connection for thousands of attendees. The support team was amazing throughout the event.',
      rating: 5,
      avatar: 'https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?auto=compress&cs=tinysrgb&w=150'
    },
    {
      id: 3,
      name: 'Emily Rodriguez',
      role: 'Digital Nomad',
      company: 'Freelancer',
      content: 'As a digital nomad, reliable internet is crucial. StarRent has been my go-to solution for the past year. Fast, reliable, and available everywhere I travel.',
      rating: 5,
      avatar: 'https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&w=150'
    }
  ]

  const stats = [
    { label: 'Happy Customers', value: '10,000+' },
    { label: 'Countries Served', value: '50+' },
    { label: 'Routers Available', value: '500+' },
    { label: 'Uptime Guarantee', value: '99.9%' }
  ]

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <Hero />

      {/* Features Section */}
      <section className="section-padding bg-white">
        <div className="container-max">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Why Choose StarRent?
            </h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              We provide the most reliable and fastest satellite internet rental service 
              with premium Starlink equipment and exceptional customer support.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {features.map((feature, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="text-center p-6 rounded-xl hover:shadow-lg transition-shadow duration-300"
              >
                <div className="feature-icon mx-auto">
                  <feature.icon className="h-6 w-6" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-3">
                  {feature.title}
                </h3>
                <p className="text-gray-600 leading-relaxed">
                  {feature.description}
                </p>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Routers Section */}
      <section className="section-padding bg-gray-50">
        <div className="container-max">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Featured Routers
            </h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Choose from our premium selection of Starlink routers, 
              each optimized for different use cases and requirements.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            {featuredRouters.map((router, index) => (
              <motion.div
                key={router.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
              >
                <RouterCard router={router} />
              </motion.div>
            ))}
          </div>

          <div className="text-center">
            <Link to="/routers" className="btn-primary group">
              View All Routers
              <ArrowRight className="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-200" />
            </Link>
          </div>
        </div>
      </section>

      {/* Pricing Section */}
      <section className="section-padding bg-white">
        <div className="container-max">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Simple, Transparent Pricing
            </h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Choose the plan that fits your needs. All plans include premium equipment, 
              setup support, and our satisfaction guarantee.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            {pricingPlans.map((plan, index) => (
              <motion.div
                key={plan.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
              >
                <PricingCard plan={plan} />
              </motion.div>
            ))}
          </div>

          <div className="text-center">
            <Link to="/pricing" className="btn-secondary group">
              Compare All Plans
              <ArrowRight className="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-200" />
            </Link>
          </div>
        </div>
      </section>

      {/* Testimonials Section */}
      <section className="section-padding bg-gray-50">
        <div className="container-max">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              What Our Customers Say
            </h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Don't just take our word for it. Here's what our satisfied customers 
              have to say about their StarRent experience.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {testimonials.map((testimonial, index) => (
              <motion.div
                key={testimonial.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="testimonial-card"
              >
                <div className="flex items-center mb-4">
                  {[...Array(testimonial.rating)].map((_, i) => (
                    <Star key={i} className="h-5 w-5 text-yellow-400 fill-current" />
                  ))}
                </div>
                
                <Quote className="h-8 w-8 text-primary-200 mb-4" />
                
                <p className="text-gray-700 mb-6 leading-relaxed">
                  "{testimonial.content}"
                </p>
                
                <div className="flex items-center">
                  <img
                    src={testimonial.avatar}
                    alt={testimonial.name}
                    className="w-12 h-12 rounded-full object-cover mr-4"
                  />
                  <div>
                    <div className="font-semibold text-gray-900">
                      {testimonial.name}
                    </div>
                    <div className="text-sm text-gray-600">
                      {testimonial.role} at {testimonial.company}
                    </div>
                  </div>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="section-padding bg-primary-600 text-white">
        <div className="container-max">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, scale: 0.5 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="text-center"
              >
                <div className="text-3xl md:text-4xl font-bold mb-2 stats-counter">
                  {stat.value}
                </div>
                <div className="text-primary-100">
                  {stat.label}
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="section-padding bg-white">
        <div className="container-max">
          <div className="text-center max-w-4xl mx-auto">
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
              Ready to Get Connected?
            </h2>
            <p className="text-xl text-gray-600 mb-8">
              Join thousands of satisfied customers who trust StarRent for their 
              satellite internet needs. Get started today with our easy rental process.
            </p>
            <div className="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
              <Link to="/routers" className="btn-primary group">
                Browse Routers
                <ArrowRight className="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-200" />
              </Link>
              <Link to="/contact" className="btn-secondary group">
                Contact Sales
                <ArrowRight className="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-200" />
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  )
}

export default Home