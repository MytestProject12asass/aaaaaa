import React from 'react'
import { Link } from 'react-router-dom'
import { ArrowRight, Play, Wifi, Globe, Zap, Shield } from 'lucide-react'
import { motion } from 'framer-motion'

const Hero: React.FC = () => {
  const stats = [
    { label: 'Active Rentals', value: '2,500+', icon: Wifi },
    { label: 'Countries Served', value: '50+', icon: Globe },
    { label: 'Uptime Guarantee', value: '99.9%', icon: Zap },
    { label: 'Customer Satisfaction', value: '98%', icon: Shield },
  ]

  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.2,
        delayChildren: 0.1,
      },
    },
  }

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: {
      opacity: 1,
      y: 0,
      transition: {
        duration: 0.6,
        ease: 'easeOut',
      },
    },
  }

  return (
    <section className="relative min-h-screen flex items-center justify-center overflow-hidden hero-pattern">
      {/* Background Elements */}
      <div className="absolute inset-0 gradient-bg opacity-5"></div>
      
      {/* Floating Elements */}
      <div className="absolute top-20 left-10 w-20 h-20 bg-primary-200 rounded-full opacity-20 floating-animation"></div>
      <div className="absolute top-40 right-20 w-16 h-16 bg-primary-300 rounded-full opacity-30 floating-animation" style={{ animationDelay: '2s' }}></div>
      <div className="absolute bottom-40 left-20 w-12 h-12 bg-primary-400 rounded-full opacity-25 floating-animation" style={{ animationDelay: '4s' }}></div>

      <div className="container-max section-padding relative z-10">
        <motion.div
          variants={containerVariants}
          initial="hidden"
          animate="visible"
          className="text-center max-w-5xl mx-auto"
        >
          {/* Badge */}
          <motion.div variants={itemVariants} className="inline-flex items-center space-x-2 bg-primary-50 text-primary-700 px-4 py-2 rounded-full text-sm font-medium mb-8">
            <span className="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            <span>Now Available Worldwide</span>
          </motion.div>

          {/* Main Heading */}
          <motion.h1
            variants={itemVariants}
            className="text-4xl md:text-6xl lg:text-7xl font-bold text-gray-900 mb-6 leading-tight hero-title"
          >
            Rent Premium{' '}
            <span className="text-gradient">Starlink Routers</span>
            <br />
            Anywhere, Anytime
          </motion.h1>

          {/* Subtitle */}
          <motion.p
            variants={itemVariants}
            className="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed"
          >
            Get high-speed satellite internet instantly with our premium Starlink router rentals. 
            Perfect for remote work, travel, events, and emergency connectivity.
          </motion.p>

          {/* CTA Buttons */}
          <motion.div
            variants={itemVariants}
            className="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6 mb-12"
          >
            <Link to="/routers" className="btn-primary group">
              Browse Routers
              <ArrowRight className="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-200" />
            </Link>
            
            <button className="flex items-center space-x-2 text-gray-700 hover:text-primary-600 transition-colors duration-200 group">
              <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-200">
                <Play className="h-5 w-5 text-primary-600 ml-1" />
              </div>
              <span className="font-medium">Watch Demo</span>
            </button>
          </motion.div>

          {/* Stats */}
          <motion.div
            variants={itemVariants}
            className="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto"
          >
            {stats.map((stat, index) => (
              <div key={index} className="text-center group">
                <div className="inline-flex items-center justify-center w-12 h-12 bg-primary-100 text-primary-600 rounded-lg mb-3 group-hover:bg-primary-600 group-hover:text-white transition-colors duration-200">
                  <stat.icon className="h-6 w-6" />
                </div>
                <div className="text-2xl md:text-3xl font-bold text-gray-900 stats-counter">
                  {stat.value}
                </div>
                <div className="text-gray-600 text-sm md:text-base">
                  {stat.label}
                </div>
              </div>
            ))}
          </motion.div>
        </motion.div>
      </div>

      {/* Scroll Indicator */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 1, duration: 0.6 }}
        className="absolute bottom-8 left-1/2 transform -translate-x-1/2"
      >
        <div className="flex flex-col items-center space-y-2 text-gray-500">
          <span className="text-sm">Scroll to explore</span>
          <div className="w-6 h-10 border-2 border-gray-300 rounded-full flex justify-center">
            <div className="w-1 h-3 bg-gray-400 rounded-full mt-2 animate-bounce"></div>
          </div>
        </div>
      </motion.div>
    </section>
  )
}

export default Hero